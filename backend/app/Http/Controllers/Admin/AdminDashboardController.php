<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Message;
use App\Models\User;
use App\Services\SystemService;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __construct(
        private readonly WhatsAppService $waService,
        private readonly SystemService $systemService
    ) {}

    public function index(): View
    {
        $diagnostics = $this->systemService->getFullDiagnostics();

        $stats = [
            'total_users'     => User::count(),
            'total_devices'   => Device::count(),
            'connected_dev'   => Device::where('status', 'connected')->count(),
            'total_messages'  => Message::count(),
            'sent_messages'   => Message::where('status', 'sent')->count(),
            'failed_messages' => Message::where('status', 'failed')->count(),
            'pending_messages'=> Message::whereIn('status', ['pending', 'processing'])->count(),
            'engine_healthy'  => $diagnostics['go_service']['online'],
            'queue'           => $diagnostics['queue'],
        ];

        $users = User::withCount(['devices', 'apiKeys'])->latest()->take(10)->get();
        $recentMessages = Message::with('device.user')->latest()->take(15)->get();
        $devices = Device::with('user')->latest()->get();

        return view('admin.dashboard', compact('stats', 'users', 'recentMessages', 'devices', 'diagnostics'));
    }

    public function getGlobalStats(): JsonResponse
    {
        $queueHealth = $this->systemService->getQueueHealth();
        $goHealth    = $this->systemService->getGoServiceHealth();

        return response()->json([
            'success' => true,
            'data' => [
                'total_users'        => User::count(),
                'total_devices'      => Device::count(),
                'connected_dev'      => Device::where('status', 'connected')->count(),
                'total_messages'     => Message::count(),
                'sent_messages'      => Message::where('status', 'sent')->count(),
                'failed_messages'    => Message::where('status', 'failed')->count(),
                'pending_messages'   => Message::whereIn('status', ['pending', 'processing'])->count(),
                'pending_jobs_queue' => $queueHealth['pending_jobs'],
                'failed_jobs_count'  => $queueHealth['failed_jobs'],
                'oldest_wait_time'   => $queueHealth['oldest_job_wait'],
                'engine_healthy'     => $goHealth['online'],
            ]
        ]);
    }

    public function getSystemHealth(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->systemService->getFullDiagnostics(),
        ]);
    }

    public function getGlobalDevices(): JsonResponse
    {
        $devices = Device::with('user:id,name,email')->latest()->get();
        return response()->json(['success' => true, 'data' => $devices]);
    }

    public function getGlobalMessages(): JsonResponse
    {
        $messages = Message::with('device.user:id,name,email')->latest()->paginate(50);
        return response()->json(['success' => true, 'data' => $messages]);
    }
}
