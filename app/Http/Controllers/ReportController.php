<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\RentalSession;
use App\Models\FoodItem;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function revenue(Request $request)
    {
        $type = $request->input('type', 'daily');
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Use Carbon for proper datetime handling
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        $query = Invoice::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDateTime, $endDateTime]);

        if ($type === 'daily') {
            $data = $query->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('SUM(console_charges) as console_revenue'),
                DB::raw('SUM(food_charges) as food_revenue'),
                DB::raw('COUNT(*) as invoice_count')
            )
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy(DB::raw('DATE(created_at)'), 'desc')
                ->get();
        } else {
            $data = $query->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('SUM(console_charges) as console_revenue'),
                DB::raw('SUM(food_charges) as food_revenue'),
                DB::raw('COUNT(*) as invoice_count')
            )
                ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
                ->orderBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), 'desc')
                ->get();
        }

        // Calculate totals
        $totalsQuery = Invoice::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDateTime, $endDateTime]);

        $totals = [
            'total_revenue' => $totalsQuery->sum('total') ?? 0,
            'console_revenue' => $totalsQuery->sum('console_charges') ?? 0,
            'food_revenue' => $totalsQuery->sum('food_charges') ?? 0,
            'invoices_count' => $totalsQuery->count() ?? 0,
        ];

        return view('reports.revenue', compact('data', 'totals', 'type', 'startDate', 'endDate'));
    }

    public function usage(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Use Carbon for proper datetime handling
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        $sessions = RentalSession::with('console.consoleType')
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->where('status', 'completed')
            ->get();

        $consoleUsage = $sessions->groupBy(function($session) {
            return $session->console && $session->console->consoleType
                ? $session->console->consoleType->name
                : 'Unknown';
        })
            ->map(function ($group) {
                $totalDuration = 0;

                foreach ($group as $session) {
                    if ($session->end_time && $session->start_time) {
                        $duration = $session->end_time->diffInMinutes($session->start_time) - ($session->total_paused_minutes ?? 0);
                        $totalDuration += max(0, $duration); // Ensure non-negative
                    }
                }

                $avgDuration = $group->count() > 0 ? $totalDuration / $group->count() : 0;

                return [
                    'sessions' => $group->count(),
                    'total_revenue' => $group->sum('total_cost') ?? 0,
                    'avg_session_duration' => round($avgDuration, 2),
                    'total_duration_hours' => round($totalDuration / 60, 2),
                ];
            });

        // Remove 'Unknown' category if it exists and has no sessions
        if (isset($consoleUsage['Unknown']) && $consoleUsage['Unknown']['sessions'] === 0) {
            unset($consoleUsage['Unknown']);
        }

        return view('reports.usage', compact('consoleUsage', 'startDate', 'endDate'));
    }

    public function topItems(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Use Carbon for proper datetime handling
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        $topFoodItems = OrderItem::select(
            'food_items.name',
            'food_items.id as food_item_id',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('SUM(order_items.subtotal) as total_revenue')
        )
            ->join('food_items', 'order_items.food_item_id', '=', 'food_items.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDateTime, $endDateTime])
            ->where('orders.payment_status', 'paid')
            ->groupBy('food_items.id', 'food_items.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        return view('reports.top-items', compact('topFoodItems', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        $type = $request->input('type');
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Use Carbon for proper datetime handling
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="report-' . $type . '-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($type, $startDateTime, $endDateTime, $startDate, $endDate) {
            $file = fopen('php://output', 'w');

            if ($type === 'revenue') {
                fputcsv($file, ['Date', 'Total Revenue', 'Console Revenue', 'Food Revenue', 'Invoice Count']);

                $data = Invoice::where('payment_status', 'paid')
                    ->whereBetween('created_at', [$startDateTime, $endDateTime])
                    ->select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(total) as revenue'),
                        DB::raw('SUM(console_charges) as console_revenue'),
                        DB::raw('SUM(food_charges) as food_revenue'),
                        DB::raw('COUNT(*) as invoice_count')
                    )
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy(DB::raw('DATE(created_at)'), 'desc')
                    ->get();

                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->date,
                        $row->revenue ?? 0,
                        $row->console_revenue ?? 0,
                        $row->food_revenue ?? 0,
                        $row->invoice_count ?? 0
                    ]);
                }
            } elseif ($type === 'usage' || $type === 'sessions') {
                fputcsv($file, ['Console Type', 'Total Sessions', 'Total Revenue', 'Avg Duration (mins)', 'Total Hours']);

                $sessions = RentalSession::with('console.consoleType')
                    ->whereBetween('created_at', [$startDateTime, $endDateTime])
                    ->where('status', 'completed')
                    ->get();

                $consoleUsage = $sessions->groupBy(function($session) {
                    return $session->console && $session->console->consoleType
                        ? $session->console->consoleType->name
                        : 'Unknown';
                })
                    ->map(function ($group) {
                        $totalDuration = 0;

                        foreach ($group as $session) {
                            if ($session->end_time && $session->start_time) {
                                $duration = $session->end_time->diffInMinutes($session->start_time) - ($session->total_paused_minutes ?? 0);
                                $totalDuration += max(0, $duration);
                            }
                        }

                        $avgDuration = $group->count() > 0 ? $totalDuration / $group->count() : 0;

                        return [
                            'sessions' => $group->count(),
                            'revenue' => $group->sum('total_cost') ?? 0,
                            'avg_duration' => round($avgDuration, 2),
                            'total_hours' => round($totalDuration / 60, 2),
                        ];
                    });

                foreach ($consoleUsage as $typeName => $data) {
                    fputcsv($file, [
                        $typeName,
                        $data['sessions'],
                        $data['revenue'],
                        $data['avg_duration'],
                        $data['total_hours']
                    ]);
                }
            } elseif ($type === 'top-items') {
                fputcsv($file, ['Item Name', 'Total Quantity', 'Total Revenue']);

                $topFoodItems = OrderItem::select(
                    'food_items.name',
                    DB::raw('SUM(order_items.quantity) as total_quantity'),
                    DB::raw('SUM(order_items.subtotal) as total_revenue')
                )
                    ->join('food_items', 'order_items.food_item_id', '=', 'food_items.id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.created_at', [$startDateTime, $endDateTime])
                    ->where('orders.payment_status', 'paid')
                    ->groupBy('food_items.id', 'food_items.name')
                    ->orderByDesc('total_revenue')
                    ->limit(10)
                    ->get();

                foreach ($topFoodItems as $item) {
                    fputcsv($file, [
                        $item->name,
                        $item->total_quantity ?? 0,
                        $item->total_revenue ?? 0
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
