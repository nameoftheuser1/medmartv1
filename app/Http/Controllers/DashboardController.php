<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\ProductBatch;
use App\Models\SaleDetail;
use App\Models\Sale;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private function predictSales($salesData, $period)
    {
        // Convert sales data to an array of values
        $dates = [];
        $sales = [];

        foreach ($salesData as $data) {
            $dates[] = strtotime($data->date);
            $sales[] = $data->total;
        }

        // Calculate the average of x and y
        $n = count($sales);
        if ($n == 0) {
            return [[], []]; // No sales data, return empty arrays
        }

        $sumX = array_sum($dates);
        $sumY = array_sum($sales);
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $dates[$i] * $sales[$i];
            $sumX2 += $dates[$i] * $dates[$i];
        }

        // Check for zero in the denominator to avoid division by zero
        $denominator = $n * $sumX2 - $sumX * $sumX;
        if ($denominator == 0) {
            // If the denominator is zero, return a default prediction (e.g., previous sales total)
            return [array_fill(0, 30, end($sales) ?? 0), []]; // Predict 30 days with last known sales or zero
        }

        // Calculate the slope (m) and intercept (b) for the line equation y = mx + b
        $slope = ($n * $sumXY - $sumX * $sumY) / $denominator;
        $intercept = ($sumY - $slope * $sumX) / $n;

        // Predict future sales based on the calculated slope and intercept
        $predictedSales = [];
        $predictedCategories = [];
        $futureDays = 30; // Predict sales for the next 30 days
        $currentDate = end($dates) + 86400; // Start predicting from the next day

        for ($i = 0; $i < $futureDays; $i++) {
            $predictedSales[] = round($slope * $currentDate + $intercept);
            $predictedCategories[] = date('D', $currentDate); // Format for display
            $currentDate += 86400; // Increment by 1 day
        }

        return [$predictedSales, $predictedCategories];
    }



    public function index(Request $request)
    {
        $period = $request->input('period', 'weekly');
        $inventoryType = $request->input('inventory-type', 'highest');

        // Handle period selection
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

        if ($inventoryType === 'highest') {
            $inventories = Inventory::select('batch_id', DB::raw('MAX(quantity) as quantity'))
                ->join('product_batches', 'inventories.batch_id', '=', 'product_batches.id')
                ->where('quantity', '>', 0)
                ->groupBy('batch_id')
                ->orderBy('quantity', 'desc')
                ->limit(10)
                ->get();
        } else {
            $inventories = Inventory::select('batch_id', DB::raw('MIN(quantity) as quantity'))
                ->join('product_batches', 'inventories.batch_id', '=', 'product_batches.id')
                ->where('quantity', '>', 0)
                ->groupBy('batch_id')
                ->orderBy('quantity', 'asc')
                ->limit(10)
                ->get();
        }

        $inventoryBatches = ProductBatch::whereIn('id', $inventories->pluck('batch_id'))
            ->get()
            ->map(function ($batch) use ($inventories) {
                $batch->quantity = $inventories->firstWhere('batch_id', $batch->id)->quantity;
                return $batch;
            });

        list($predictedSales, $predictedCategories) = $this->predictSales($salesData, $period);

        if ($request->ajax()) {
            return response()->json([
                'categories' => $categories,
                'salesSeries' => $salesSeries,
                'predictedSales' => $predictedSales,
                'predictedCategories' => $predictedCategories,
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
            'predictedCategories' => $predictedCategories,
            'totalSales' => $totalSales,
            'currentPeriod' => $period,
            'currentInventoryType' => $inventoryType,
            'inventoryBatches' => $inventoryBatches,
            'fastMovingProducts' => $fastMovingProducts,
        ]);
    }
}
