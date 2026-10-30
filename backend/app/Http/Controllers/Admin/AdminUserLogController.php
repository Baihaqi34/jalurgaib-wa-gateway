<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserLoginLog;
use App\Services\AuthenticationLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserLogController extends Controller
{
    public function __construct(
        protected AuthenticationLogService $logService
    ) {
        $this->logService->ensureTableExists();
    }

    /**
     * Display a listing of user login and logout logs with telemetry and filters.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $event  = $request->input('event');
        $status = $request->input('status');
        $role   = $request->input('role');

        $query = UserLoginLog::query()->with('user');

        // Apply filters
        $query->search($search)
              ->filterEvent($event)
              ->filterStatus($status);

        if (!empty($role) && $role !== 'all') {
            $query->where('role', $role);
        }

        // Recent logs first
        $logs = $query->recent()->paginate(25)->withQueryString();

        // Telemetry KPI Stats (Today's activities)
        $todayStart = now()->startOfDay();
        $stats = [
            'total_today'   => UserLoginLog::where('created_at', '>=', $todayStart)->count(),
            'login_today'   => UserLoginLog::where('created_at', '>=', $todayStart)->where('event', 'login')->count(),
            'payment_today' => UserLoginLog::where('created_at', '>=', $todayStart)->where('event', 'payment_success')->count(),
            'failed_today'  => UserLoginLog::where('created_at', '>=', $todayStart)->whereIn('event', ['login_failed', 'payment_failed', 'payment_canceled'])->count(),
        ];

        return view('admin.logs.index', compact('logs', 'stats', 'search', 'event', 'status', 'role'));
    }

    /**
     * Clear authentication logs older than 30 days.
     */
    public function clearOld(Request $request): RedirectResponse
    {
        $deleted = UserLoginLog::where('created_at', '<', now()->subDays(30))->delete();

        return redirect()->route('admin.logs.index')
            ->with('success', "Berhasil membersihkan {$deleted} data log yang berusia lebih dari 30 hari.");
    }
}
