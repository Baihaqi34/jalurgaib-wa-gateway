<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Device;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Jobs\SendWhatsAppMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function __construct(private readonly WhatsAppService $waService) {}

    private function getUser(Request $request): User
    {
        $user = $request->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }
        return $user;
    }

    /**
     * Send a single text message (queued).
     */
    public function sendText(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id'  => 'required|string|exists:devices,device_id',
            'to'         => 'required|string|max:20',
            'message'    => 'required|string|max:4096',
            'media_url'  => 'nullable|string|url|max:2048',
            'image'      => 'nullable|image|max:10240', // Maks 10MB
            'min_delay'  => 'nullable|integer|min:500|max:10000',
            'max_delay'  => 'nullable|integer|min:1000|max:30000',
        ]);

        $user = $this->getUser($request);

        // Strictly verify that the device belongs to the authenticated user
        $device = Device::where('device_id', $validated['device_id'])
            ->when(!$user->isAdmin(), fn($q) => $q->where('user_id', $user->id))
            ->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat WhatsApp tidak ditemukan pada akun Anda atau milik pengguna lain.',
            ], 403);
        }

        // Sync latest status from Go service
        $this->waService->getDeviceStatus($device);

        if (!$device->isConnected()) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat WhatsApp (' . $device->name . ') belum terhubung/offline.',
            ], 422);
        }

        // 🔒 Enforce Daily Message Limit based on user's active package
        if (!$user->canSendMessages(1)) {
            $pkg = $user->getPackage();
            return response()->json([
                'success'         => false,
                'message'         => "Kuota pesan harian untuk paket {$pkg->name} (Maks {$user->getDailyMessageLimit()} pesan/hari) telah habis. Kuota di-reset setiap pukul 00:00 WIB.",
                'error_code'      => 'DAILY_MESSAGE_LIMIT_REACHED',
                'daily_limit'     => $user->getDailyMessageLimit(),
                'used_today'      => $user->getTodaySentMessagesCount(),
                'remaining_today' => $user->getRemainingDailyMessages(),
            ], 429);
        }

        // Normalize phone number to 62 format
        $toPhone = preg_replace('/[^0-9]/', '', $validated['to']);
        if (str_starts_with($toPhone, '0')) {
            $toPhone = '62' . substr($toPhone, 1);
        } elseif (str_starts_with($toPhone, '8')) {
            $toPhone = '62' . $toPhone;
        }

        // Handle uploaded image file if provided (Save directly into public/uploads)
        $mediaUrl = $validated['media_url'] ?? null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');
            $filename = 'wa_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $filename);
            $mediaUrl = asset('uploads/' . $filename);
            
            \Illuminate\Support\Facades\Log::info('[WA Message] Image saved to public/uploads', [
                'filename' => $filename,
                'url'      => $mediaUrl,
            ]);
        }

        $message = Message::create([
            'device_id' => $device->id,
            'to'        => $toPhone,
            'message'   => $validated['message'],
            'type'      => $mediaUrl ? 'image' : 'text',
            'media_url' => $mediaUrl,
            'status'    => 'pending',
            'min_delay' => $validated['min_delay'] ?? 1000,
            'max_delay' => $validated['max_delay'] ?? 4000,
        ]);

        // Dispatch to queue
        SendWhatsAppMessage::dispatch($message, $device);

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dimasukkan ke antrean delivery Queue.',
            'data'    => [
                'message_id'      => $message->id,
                'status'          => $message->status,
                'remaining_quota' => $user->getRemainingDailyMessages(),
            ],
        ], 202);
    }

    /**
     * Send bulk messages (batch with queue).
     */
    public function sendBulk(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id'    => 'required|string|exists:devices,device_id',
            'recipients'   => 'required|array|min:1|max:100',
            'recipients.*' => 'required|string|max:20',
            'message'      => 'required|string|max:4096',
            'min_delay'    => 'nullable|integer|min:2000|max:30000',
            'max_delay'    => 'nullable|integer|min:5000|max:60000',
        ]);

        $user = $this->getUser($request);

        // Strictly verify that the device belongs to the authenticated user
        $device = Device::where('device_id', $validated['device_id'])
            ->when(!$user->isAdmin(), fn($q) => $q->where('user_id', $user->id))
            ->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat WhatsApp tidak ditemukan pada akun Anda atau milik pengguna lain.',
            ], 403);
        }

        // Sync latest status from Go service
        $this->waService->getDeviceStatus($device);

        if (!$device->isConnected()) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat WhatsApp (' . $device->name . ') belum terhubung/offline.',
            ], 422);
        }

        $recipientCount = count($validated['recipients']);

        // 🔒 Enforce Daily Message Limit for Bulk sending
        if (!$user->canSendMessages($recipientCount)) {
            $pkg = $user->getPackage();
            $remaining = $user->getRemainingDailyMessages();
            return response()->json([
                'success'         => false,
                'message'         => "Pengiriman batch ({$recipientCount} pesan) melebihi sisa kuota harian paket {$pkg->name} ({$remaining} tersisa dari {$user->getDailyMessageLimit()}/hari).",
                'error_code'      => 'DAILY_MESSAGE_LIMIT_EXCEEDED',
                'daily_limit'     => $user->getDailyMessageLimit(),
                'used_today'      => $user->getTodaySentMessagesCount(),
                'remaining_today' => $remaining,
                'requested_count' => $recipientCount,
            ], 429);
        }

        $queued = [];
        $delay  = 0;

        foreach ($validated['recipients'] as $recipient) {
            $message = Message::create([
                'device_id' => $device->id,
                'to'        => $recipient,
                'message'   => $validated['message'],
                'type'      => 'text',
                'status'    => 'pending',
                'min_delay' => $validated['min_delay'] ?? 2000,
                'max_delay' => $validated['max_delay'] ?? 6000,
            ]);

            SendWhatsAppMessage::dispatch($message, $device)->delay(now()->addSeconds($delay));
            $delay += 5;

            $queued[] = ['message_id' => $message->id, 'to' => $recipient];
        }

        return response()->json([
            'success' => true,
            'message' => count($queued) . ' pesan berhasil dimasukkan ke antrean delivery Queue.',
            'data'    => $queued,
        ], 202);
    }

    /**
     * Get message history for the authenticated user ONLY.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $this->getUser($request);
        $perPage = (int) $request->query('per_page', 10);
        $perPage = min(max($perPage, 5), 100);
        
        $messages = Message::whereHas('device', fn($q) => $q->where('user_id', $user->id))
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $messages,
        ]);
    }
}
