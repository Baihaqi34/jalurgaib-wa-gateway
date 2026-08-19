<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Message;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $package = $user->getPackage();

        $todaySent = $user->getTodaySentMessagesCount();
        $dailyLimit = $user->getDailyMessageLimit();
        $remainingDaily = $user->getRemainingDailyMessages();
        $maxDevices = $user->getMaxDevices();
        $devicesCount = $user->devices()->count();

        return view('user.dashboard', compact(
            'user', 
            'package', 
            'todaySent', 
            'dailyLimit', 
            'remainingDaily', 
            'maxDevices', 
            'devicesCount'
        ));
    }

    public function upgrade(): View
    {
        $user = auth()->user();
        $package = $user->getPackage();
        $packages = \App\Models\Package::whereIn('status', ['active', 'coming_soon'])
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $todaySent = $user->getTodaySentMessagesCount();
        $dailyLimit = $user->getDailyMessageLimit();
        $maxDevices = $user->getMaxDevices();
        $devicesCount = $user->devices()->count();

        return view('user.upgrade', compact(
            'user', 
            'package', 
            'packages', 
            'todaySent', 
            'dailyLimit', 
            'maxDevices', 
            'devicesCount'
        ));
    }

    public function __construct(private readonly WhatsAppService $waService) {}

    public function getStats(): JsonResponse
    {
        $user = auth()->user();
        $devices = $user->devices()->get();

        foreach ($devices as $device) {
            $this->waService->getDeviceStatus($device);
        }

        $deviceIds = $user->devices()->pluck('id');

        return response()->json([
            'success' => true,
            'data' => [
                'total_devices'    => $user->devices()->count(),
                'max_devices'      => $user->getMaxDevices(),
                'connected_dev'    => $user->devices()->where('status', 'connected')->count(),
                'total_messages'   => Message::whereIn('device_id', $deviceIds)->count(),
                'sent_messages'    => Message::whereIn('device_id', $deviceIds)->where('status', 'sent')->count(),
                'today_messages'   => $user->getTodaySentMessagesCount(),
                'daily_limit'      => $user->getDailyMessageLimit(),
                'remaining_today'  => $user->getRemainingDailyMessages(),
                'package_name'     => $user->getPackage()->name,
                'package_badge'    => $user->getPackage()->badge ?? 'Standard',
                'pending_messages' => Message::whereIn('device_id', $deviceIds)->whereIn('status', ['pending', 'processing'])->count(),
                'total_api_keys'   => $user->apiKeys()->count(),
            ]
        ]);
    }
}
