<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserOrderController extends Controller
{
    /**
     * Display a listing of the user's payment & invoice history.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $package = $user->getPackage();

        // Auto-expire pending orders exceeding the expiration window (Kasera default 60 minutes)
        $expiresInMinutes = (int) config('services.kasera.expires_in_minutes', 60);
        $user->orders()
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes($expiresInMinutes))
            ->update(['status' => 'expired']);

        // Build query with optional status filter and order_no search
        $query = $user->orders()->with('package')->latest();

        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        if ($search = $request->input('search')) {
            $query->where('order_no', 'like', "%{$search}%");
        }

        $orders = $query->paginate(15)->withQueryString();

        // Summary metrics for KPI widgets
        $stats = [
            'total'   => $user->orders()->count(),
            'pending' => $user->orders()->where('status', 'pending')->count(),
            'paid'    => $user->orders()->where('status', 'paid')->count(),
            'expired' => $user->orders()->whereIn('status', ['expired', 'canceled', 'failed'])->count(),
        ];

        return view('user.orders', compact('user', 'package', 'orders', 'stats', 'expiresInMinutes'));
    }
}
