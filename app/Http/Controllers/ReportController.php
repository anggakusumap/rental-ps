<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\RentalSession;
use App\Models\FoodItem;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function revenue(Request $request)
    {
        $type = $request->input('type', 'daily');
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $query = Invoice::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($type === 'daily') {
            $data = $query->selectRaw('DATE(created_at) as date, SUM(total) as revenue, SUM(console_charges) as console_revenue, SUM(food_charges) as food_revenue')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();
        } else {
            $data = $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total) as revenue, SUM(console_charges) as console_revenue, SUM(food_charges) as food_revenue')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->get();
        }

        $totals = [
            'total_revenue' => $query->sum('total'),
            'console_revenue' => $query->sum('console_charges'),
            'food_revenue' => $query->sum('food_charges'),
            'invoices_count' => $query->count(),
        ];

        return view('reports.revenue', compact('data', 'totals', 'type', 'startDate', 'endDate'));
    }

    public function usage(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $sessions = RentalSession::with('console.consoleType')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        $consoleUsage = $sessions->groupBy('console.consoleType.name')
            ->map(function ($group) {
                return [
                    'sessions' => $group->count(),
                    'total_revenue' => $group->sum('total_cost'),
                    'avg_session_duration' => $group->avg(function ($session) {
                        return $session->end_time->diffInMinutes($session->start_time) - $session->total_paused_minutes;
                    }),
                ];
            });

        return view('reports.usage', compact('consoleUsage', 'startDate', 'endDate'));
    }

    public function topItems(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $topFoodItems = OrderItem::select('food_items.name', DB::raw('SUM(order_items.quantity) as total_quantity'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->join('food_items', 'order_items.food_item_id', '=', 'food_items.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('food_items.id', 'food_items.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        return view('reports.top-items', compact('topFoodItems', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        $type = $request->input('type');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="report-' . $type . '-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($type, $request) {
            $file = fopen('php://output', 'w');

            if ($type === 'revenue') {
                fputcsv($file, ['Date', 'Total Revenue', 'Console Revenue', 'Food Revenue']);

                $startDate = $request->input('start_date', now()->startOfMonth());
                $endDate = $request->input('end_date', now()->endOfMonth());

                $data = Invoice::where('payment_status', 'paid')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, SUM(console_charges) as console_revenue, SUM(food_charges) as food_revenue')
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
                    ->get();

                foreach ($data as $row) {
                    fputcsv($file, [$row->date, $row->revenue, $row->console_revenue, $row->food_revenue]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
