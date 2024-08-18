<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\ProductBatch;
use App\Models\Sale;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'weekly');

        switch ($period) {
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth()->subMonths(11);
                $endDate = Carbon::now()->endOfMonth();
                $groupBy = 'DATE_FORMAT(created_at, "%Y-%m")';
                $dateFormat = 'Y-m';
                $displayFormat = 'M Y';
                break;
            case 'yearly':
                $startDate = Carbon::now()->startOfYear()->subYears(4);
                $endDate = Carbon::now()->endOfYear();
                $groupBy = 'YEAR(created_at)';
                $dateFormat = 'Y';
                $displayFormat = 'Y';
                break;
            default:
                $startDate = Carbon::now()->subDays(6);
                $endDate = Carbon::now();
                $groupBy = 'DATE(created_at)';
                $dateFormat = 'Y-m-d';
                $displayFormat = 'D';
        }

        $salesData = Sale::select(DB::raw("$groupBy as date"), DB::raw('SUM(total_amount) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        $categories = [];
        $salesSeries = [];
        $totalSales = 0;

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $formattedDate = $currentDate->format($dateFormat);
            $categories[] = $currentDate->format($displayFormat);
            $dailySales = $salesData->get($formattedDate)->total ?? 0;
            $salesSeries[] = $dailySales;
            $totalSales += $dailySales;

            if ($period === 'weekly') {
                $currentDate->addDay();
            } elseif ($period === 'monthly') {
                $currentDate->addMonth();
            } else {
                $currentDate->addYear();
            }
        }

        $productCount = Product::count();
        $supplierCount = Supplier::count();

        $thresholdDate = Carbon::now()->addDays(30);

        $expiringBatches = ProductBatch::with(['product', 'inventories'])
            ->where('expiration_date', '<=', $thresholdDate)
            ->whereHas('inventories', function ($query) {
                $query->where('quantity', '>', 0);
            })
            ->orderBy('expiration_date', 'asc')
            ->paginate(5);

        $totalSalesToday = Sale::whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        return view('dashboard.index', [
            'productCount' => $productCount,
            'supplierCount' => $supplierCount,
            'expiringBatches' => $expiringBatches,
            'totalSalesToday' => $totalSalesToday,
            'categories' => $categories,
            'salesSeries' => $salesSeries,
            'totalSales' => $totalSales,
            'currentPeriod' => $period,
        ]);
    }
}
