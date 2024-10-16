<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\ProductBatch;
use App\Models\SaleDetail;
use App\Models\Sale;
use App\Models\Inventory;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private function predictSales($salesData)
    {
        // Convert collection to array if needed
        if ($salesData instanceof \Illuminate\Support\Collection) {
            $salesData = $salesData->toArray();
        }

        // Retrieve settings for predicted sales day and historical data days
        $predictedSalesDay = Setting::where('key', 'predictedSalesDay')->value('value') ?? 1;
        $historicalDataDays = Setting::where('key', 'historicalDataDays')->value('value') ?? 90;

        // Initialize arrays for dates and sales
        $dates = [];
        $sales = [];
        $cutoffDate = strtotime("-{$historicalDataDays} days");

        // Filter sales data based on the cutoff date
        foreach ($salesData as $data) {
            if (!isset($data['date'], $data['total_amount'])) {
                continue;
            }

            $totalAmount = (float) $data['total_amount'];
            $dataDate = strtotime($data['date']);

            // Only include sales data that is after the cutoff date
            if ($dataDate >= $cutoffDate) {
                $dates[] = $dataDate;
                $sales[] = $totalAmount;
            }
        }

        $n = count($sales);

        // Handle cases with insufficient sales data
        if ($n < 2) {
            $defaultSalesValue = !empty($sales) ? end($sales) : 0;

            return [
                'sales' => array_fill(0, 10, max(0, $defaultSalesValue)),
                'dates' => array_map(function ($i) use ($predictedSalesDay) {
                    return date('Y-m-d', strtotime("+{$i} days"));
                }, range(0, 9 * $predictedSalesDay, $predictedSalesDay))
            ];
        }

        // Calculate sums needed for linear regression
        $sumX = array_sum($dates);
        $sumY = array_sum($sales);
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $dates[$i] * $sales[$i];
            $sumX2 += $dates[$i] * $dates[$i];
        }

        $denominator = $n * $sumX2 - $sumX * $sumX;

        // Handle case where denominator is zero
        if ($denominator == 0) {
            $defaultValue = end($sales) ?? 0;

            return [
                'sales' => array_fill(0, 10, $defaultValue),
                'dates' => array_map(function ($i) use ($predictedSalesDay) {
                    return date('Y-m-d', strtotime("+{$i} days"));
                }, range(0, 9 * $predictedSalesDay, $predictedSalesDay))
            ];
        }

        // Calculate slope and intercept for linear regression
        $slope = ($n * $sumXY - $sumX * $sumY) / $denominator;
        $intercept = ($sumY - $slope * $sumX) / $n;

        // Prepare arrays for predicted sales and dates
        $predictedSales = [];
        $predictedDates = [];
        $currentDate = strtotime('today') + 86400 * $predictedSalesDay;
        $maxPredictions = 10;

        // Generate predicted sales for the next days
        for ($i = 0; $i < $maxPredictions; $i++) {
            $predictedValue = max(0, round($slope * $currentDate + $intercept));
            $predictedSales[] = $predictedValue;
            $predictedDates[] = date('Y-m-d', $currentDate);
            $currentDate += 86400 * $predictedSalesDay;
        }

        return ['sales' => $predictedSales, 'dates' => $predictedDates];
    }

    public function index(Request $request)
    {
        $period = $request->input('period', 'weekly');
        $inventoryType = $request->input('inventory-type', 'highest');

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

        $historicalDataDays = Setting::where('key', 'historicalDataDays')->value('value') ?? 90;
        $historicalEndDate = Carbon::now();
        $historicalStartDate = Carbon::now()->subDays($historicalDataDays);

        $historicalSalesData = Sale::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as date"),
            DB::raw('SUM(total_amount) as total')
        )
            ->whereBetween(DB::raw('DATE(created_at)'), [$historicalStartDate->format('Y-m-d'), $historicalEndDate->format('Y-m-d')])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($data) {
                return [
                    'date' => $data->date,
                    'total_amount' => $data->total
                ];
            })
            ->keyBy('date');


        $productCount = Product::count();
        $supplierCount = Supplier::count();
        $saleCount = Sale::count();

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

        $fastMovingProducts = SaleDetail::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->whereHas('sales', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->with('product')
            ->get()
            ->map(function ($saleDetail) {
                $product = $saleDetail->product;
                return [
                    'product_id' => $product->id,
                    'product_name' => $product->product_name,
                    'generic_name' => $product->generic_name,
                    'category' => $product->category,
                    'total_quantity' => $saleDetail->total_quantity,
                    'price' => $product->price,
                ];
            });

        $inventoryQuery = Inventory::select('batch_id', DB::raw('SUM(quantity) as quantity'))
            ->join('product_batches', 'inventories.batch_id', '=', 'product_batches.id')
            ->where('quantity', '>', 0)
            ->groupBy('batch_id');

        if ($inventoryType === 'highest') {
            $inventoryQuery->orderBy('quantity', 'desc');
        } else {
            $inventoryQuery->orderBy('quantity', 'asc');
        }

        $inventories = $inventoryQuery->limit(10)->get();

        $inventoryBatches = ProductBatch::whereIn('id', $inventories->pluck('batch_id'))
            ->with('product')
            ->get()
            ->map(function ($batch) use ($inventories) {
                $batch->quantity = $inventories->firstWhere('batch_id', $batch->id)->quantity;
                return $batch;
            });

        $prediction = $this->predictSales($historicalSalesData);
        $predictedSales = $prediction['sales'];
        $predictedDates = $prediction['dates'];

        if ($request->ajax()) {
            return response()->json([
                'categories' => $categories,
                'salesSeries' => $salesSeries,
                'predictedSales' => $predictedSales,
                'predictedDates' => $predictedDates,
                'inventoryBatches' => $inventoryBatches,
            ]);
        }

        return view('dashboard.index', [
            'productCount' => $productCount,
            'supplierCount' => $supplierCount,
            'saleCount' => $saleCount,
            'expiringBatches' => $expiringBatches,
            'totalSalesToday' => $totalSalesToday,
            'categories' => $categories,
            'salesSeries' => $salesSeries,
            'predictedSales' => $predictedSales,
            'predictedDates' => $predictedDates,
            'totalSales' => $totalSales,
            'currentPeriod' => $period,
            'currentInventoryType' => $inventoryType,
            'inventoryBatches' => $inventoryBatches,
            'fastMovingProducts' => $fastMovingProducts,
        ]);
    }
}
