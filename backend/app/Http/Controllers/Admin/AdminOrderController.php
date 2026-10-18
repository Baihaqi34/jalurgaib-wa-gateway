<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    /**
     * Display a listing of all customer orders/transactions.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $orders = Order::with(['user', 'package'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total_volume'   => Order::where('status', 'paid')->sum('amount'),
            'paid_count'     => Order::where('status', 'paid')->count(),
            'pending_count'  => Order::where('status', 'pending')->count(),
            'failed_count'   => Order::whereIn('status', ['failed', 'expired', 'canceled'])->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats', 'status'));
    }
}
