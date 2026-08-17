<?php

namespace App\Services;

use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SystemService
{
    /**
     * Get detailed health diagnostics for Go WhatsApp Service.
     */
    public function getGoServiceHealth(): array
    {
        $goUrl = config('services.wa_service.url', 'http://127.0.0.1:8080');
        $data = [
            'online'         => false,
            'url'            => $goUrl,
            'active_devices' => [],
            'device_count'   => 0,
            'response_ms'    => 0,
            'status_label'   => 'Offline (Port 8080 tidak merespon)',
        ];

        $start = microtime(true);
        try {
            $response = Http::timeout(2)
                ->withHeaders(['X-API-Key' => config('services.wa_service.api_key', 'wa-service-secret-key-2024')])
                ->get($goUrl . '/api/health');

            $data['response_ms'] = round((microtime(true) - $start) * 1000, 2);

            if ($response->successful()) {
                $json = $response->json();
                $data['online'] = true;
                $data['active_devices'] = $json['active_devices'] ?? [];
                $data['device_count'] = $json['total_active'] ?? count($data['active_devices']);
                $data['status_label'] = 'Online & Siap';
            }
        } catch (\Exception $e) {
            $data['error'] = $e->getMessage();
        }

        return $data;
    }

    /**
     * Get real-time queue worker diagnostics and wait times.
     */
    public function getQueueHealth(): array
    {
        $pendingJobs = DB::table('jobs')->count();
        $failedJobs  = DB::table('failed_jobs')->count();

        // Oldest job in queue
        $oldestJob = DB::table('jobs')->orderBy('created_at', 'asc')->first();
        $oldestJobWait = $oldestJob
            ? Carbon::createFromTimestamp($oldestJob->created_at)->diffForHumans(null, true)
            : '0 detik (Antrean Kosong)';

        // Oldest pending message
        $oldestPendingMsg = Message::whereIn('status', ['pending', 'processing'])->oldest()->first();
        $msgWaitTime = $oldestPendingMsg
            ? Carbon::parse($oldestPendingMsg->created_at)->diffForHumans(null, true)
            : 'Tidak ada antrean tertunda';

        return [
            'pending_jobs'    => $pendingJobs,
            'failed_jobs'     => $failedJobs,
            'oldest_job_wait' => $oldestJobWait,
            'msg_wait_time'   => $msgWaitTime,
            'worker_status'   => $pendingJobs > 0 ? 'Sedang Memproses Antrean' : 'Siap / Standby (Idle)',
            'worker_online'   => true,
        ];
    }

    /**
     * Get system environment stats.
     */
    public function getServerStats(): array
    {
        return [
            'php_version'     => PHP_VERSION,
            'laravel_version' => app()->version(),
            'memory_usage'    => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'server_time'     => now()->toDateTimeString(),
            'timezone'        => config('app.timezone', 'UTC'),
        ];
    }

    /**
     * Get full aggregated diagnostics.
     */
    public function getFullDiagnostics(): array
    {
        return [
            'go_service' => $this->getGoServiceHealth(),
            'queue'      => $this->getQueueHealth(),
            'server'     => $this->getServerStats(),
        ];
    }
}
