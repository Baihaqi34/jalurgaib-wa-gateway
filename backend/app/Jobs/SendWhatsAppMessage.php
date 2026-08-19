<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\Message;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of retries before the job fails permanently.
     */
    public int $tries = 3;

    /**
     * Delay between retries (seconds).
     */
    public int $backoff = 30;

    public function __construct(
        public readonly Message $message,
        public readonly Device $device,
    ) {}

    public function handle(WhatsAppService $waService): void
    {
        Log::info('[WA Job] Sending message', [
            'message_id' => $this->message->id,
            'device_id'  => $this->device->device_id,
            'to'         => $this->message->to,
        ]);

        // Sync live status from Go service
        $waService->getDeviceStatus($this->device);

        if (!$this->device->fresh()->isConnected()) {
            Log::warning('[WA Job] Device not connected, failing job', [
                'device_id' => $this->device->device_id,
            ]);
            $this->fail('Device not connected');
            return;
        }

        $result = $waService->sendText($this->message, $this->device);

        if (!($result['success'] ?? false)) {
            $errMsg = $result['message'] ?? 'Gagal terhubung atau WhatsApp offline';
            Log::warning('[WA Job] Send failed', [
                'error' => $errMsg,
            ]);
            $this->message->update([
                'status'        => 'failed',
                'error_message' => $errMsg,
            ]);

            // Jika error 463 (nomor tidak valid/tidak ada di WA), jangan retry terus-menerus
            if (str_contains($errMsg, '463') || str_contains($errMsg, 'not registered')) {
                $this->fail(new \Exception($errMsg));
                return;
            }

            $this->release($this->backoff);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('[WA Job] Job permanently failed', [
            'message_id' => $this->message->id,
            'error'      => $exception->getMessage(),
        ]);

        $this->message->update([
            'status'        => 'failed',
            'error_message' => $exception->getMessage(),
        ]);
    }
}
