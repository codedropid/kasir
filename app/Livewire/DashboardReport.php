<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Laporan Omset & Analisis - Kafe POS')]
class DashboardReport extends Component
{
    public string $period = 'today'; // today, this_week, this_month, all
    public ?int $selectedOrderForDetail = null;

    public function selectPeriod(string $period): void
    {
        if (in_array($period, ['today', 'this_week', 'this_month', 'all'], true)) {
            $this->period = $period;
        }
    }

    public function viewOrderDetail(int $orderId): void
    {
        $this->selectedOrderForDetail = $orderId;
    }

    public function closeOrderDetail(): void
    {
        $this->selectedOrderForDetail = null;
    }

    public function render()
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Hanya Administrator yang berhak mengakses laporan.');
        }

        $now = Carbon::now();

        // 1. Overall Quick Stats
        $todayRevenue = Order::where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->sum('final_amount');

        $todayOrdersCount = Order::where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->count();

        $monthRevenue = Order::where('status', 'completed')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('final_amount');

        $monthOrdersCount = Order::where('status', 'completed')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        // 2. Filtered Query for dynamic report view
        $ordersQuery = Order::with(['user', 'paymentMethod', 'items.product'])->where('status', 'completed');

        if ($this->period === 'today') {
            $ordersQuery->whereDate('created_at', Carbon::today());
        } elseif ($this->period === 'this_week') {
            $ordersQuery->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
        } elseif ($this->period === 'this_month') {
            $ordersQuery->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
        }

        $periodRevenue = (clone $ordersQuery)->sum('final_amount');
        $periodOrdersCount = (clone $ordersQuery)->count();
        $recentOrders = (clone $ordersQuery)->orderBy('created_at', 'desc')->take(20)->get();

        // 3. Best Seller Products
        $bestSellers = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product.category')
            ->take(6)
            ->get();

        $detailOrder = $this->selectedOrderForDetail ? Order::with(['items.product', 'user', 'paymentMethod'])->find($this->selectedOrderForDetail) : null;

        return view('livewire.dashboard-report', [
            'todayRevenue' => $todayRevenue,
            'todayOrdersCount' => $todayOrdersCount,
            'monthRevenue' => $monthRevenue,
            'monthOrdersCount' => $monthOrdersCount,
            'periodRevenue' => $periodRevenue,
            'periodOrdersCount' => $periodOrdersCount,
            'recentOrders' => $recentOrders,
            'bestSellers' => $bestSellers,
            'detailOrder' => $detailOrder,
        ]);
    }
}
