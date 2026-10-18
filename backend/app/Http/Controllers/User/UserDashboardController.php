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
        $deviceIds = $user->devices()->pluck('id');
        $recentMessages = Message::whereIn('device_id', $deviceIds)->with('device')->latest()->take(5)->get();
        $devices = $user->devices()->latest()->take(4)->get();

        return view('user.dashboard', compact(
            'user', 
            'package', 
            'todaySent', 
            'dailyLimit', 
            'remainingDaily', 
            'maxDevices', 
            'devicesCount',
            'recentMessages',
            'devices'
        ));
    }

    public function messages(): View
    {
        $user = auth()->user();
        $package = $user->getPackage();
        $devices = $user->devices()->where('status', 'connected')->get();
        $allDevices = $user->devices()->get();
        $todaySent = $user->getTodaySentMessagesCount();
        $dailyLimit = $user->getDailyMessageLimit();
        $remainingDaily = $user->getRemainingDailyMessages();

        return view('user.messages', compact(
            'user', 
            'package', 
            'devices', 
            'allDevices', 
            'todaySent', 
            'dailyLimit', 
            'remainingDaily'
        ));
    }

    public function devices(): View
    {
        $user = auth()->user();
        $package = $user->getPackage();
        $devices = $user->devices()->latest()->get();
        $maxDevices = $user->getMaxDevices();
        $devicesCount = $devices->count();

        foreach ($devices as $device) {
            $this->waService->getDeviceStatus($device);
        }

        return view('user.devices', compact(
            'user', 
            'package', 
            'devices', 
            'maxDevices', 
            'devicesCount'
        ));
    }

    public function history(Request $request): View
    {
        $user = auth()->user();
        $package = $user->getPackage();
        $deviceIds = $user->devices()->pluck('id');

        $query = Message::whereIn('device_id', $deviceIds)->with('device')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('to', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        $messages = $query->paginate(20)->withQueryString();
        $devices = $user->devices()->get();

        return view('user.history', compact('user', 'package', 'messages', 'devices'));
    }

    public function apiKeys(): View
    {
        $user = auth()->user();
        $package = $user->getPackage();
        $apiKeys = $user->apiKeys()->latest()->get();

        return view('user.api-keys', compact('user', 'package', 'apiKeys'));
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
        // Auto-expire pending orders that exceed the expiration window (default 60 minutes)
        $expiresInMinutes = (int) config('services.kasera.expires_in_minutes', 60);
        $user->orders()
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes($expiresInMinutes))
            ->update(['status' => 'expired']);

        $recentOrders = $user->orders()->with('package')->latest()->take(10)->get();

        return view('user.upgrade', compact(
            'user', 
            'package', 
            'packages', 
            'todaySent', 
            'dailyLimit', 
            'maxDevices', 
            'devicesCount',
            'recentOrders'
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
