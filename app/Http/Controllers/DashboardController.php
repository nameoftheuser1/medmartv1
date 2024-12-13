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
use Illuminate\Support\Facades\Log;
use Phpml\Regression\LeastSquares;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Set period to 'monthly' by default, and ignore user input for periods
        $period = 'monthly';
        $inventoryType = $request->input('inventory-type', 'highest');

        // Get date range and formats based on the monthly period
        $dateSettings = $this->getDateRangeAndFormats($period);
        $startDate = $dateSettings['startDate'];
        $endDate = $dateSettings['endDate'];
        $groupBy = $dateSettings['groupBy'];
        $dateFormat = $dateSettings['dateFormat'];
        $displayFormat = $dateSettings['displayFormat'];

        // Get sales data grouped by month
        $salesData = Sale::select(DB::raw("$groupBy as date"), DB::raw('SUM(total_amount) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        $categories = [];
        $salesSeries = [];

        // Get total sales for the period
        $totalSales = DB::table('sales')
            ->where('status', 'complete')
            ->sum('total_amount');

        // Loop through each month between startDate and endDate
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $formattedDate = $currentDate->format($dateFormat);
            $categories[] = $currentDate->format($displayFormat);
            $monthlySales = $salesData->get($formattedDate)->total ?? 0;
            $salesSeries[] = $monthlySales;

            // Move to the next month
            $currentDate->addMonth();
        }

        // Additional data
        $counts = $this->getCounts();
        $productCount = $counts['productCount'];
        $supplierCount = $counts['supplierCount'];
        $saleCount = $counts['saleCount'];

        $thresholdDate = Carbon::now()->addMonths(3);
        $expiringBatches = ProductBatch::with(['product', 'inventories'])
            ->where('expiration_date', '>', now())
            ->where('expiration_date', '<=', $thresholdDate)
            ->whereHas('inventories', function ($query) {
                $query->where('quantity', '>', 0);
            })
            ->orderBy('expiration_date', 'asc')
            ->paginate(30);

        $totalSalesToday = Sale::whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        $fastMovingProducts = $this->getFastMovingProducts($startDate, $endDate);

        // Get inventory batches
        $inventoryBatches = $this->getInventoryBatches($inventoryType);

        // Get predicted sales data
        $prediction = $this->predictSales();
        $predictedSales = $prediction['sales'];
        $predictedDates = $prediction['dates'];

        // Return JSON response if AJAX request
        if ($request->ajax()) {
            return response()->json([
                'categories' => $categories,
                'salesSeries' => $salesSeries,
                'predictedSales' => $predictedSales,
                'predictedDates' => $predictedDates,
                'inventoryBatches' => $inventoryBatches,
            ]);
        }

        // Return the dashboard view with the required data
        $historicalData = $salesSeries; // Historical sales data (last 11 months)
        $predictedData = $predictedSales; // Predicted sales data (next 3 months)
        $categories = array_merge($categories, $predictedDates); // Merge historical and predicted dates

        // Prepare for JavaScript rendering
        return view('dashboard.index', [
            'productCount' => $productCount,
            'supplierCount' => $supplierCount,
            'saleCount' => $saleCount,
            'expiringBatches' => $expiringBatches,
            'totalSalesToday' => $totalSalesToday,
            'categories' => $categories,
            'salesSeries' => $historicalData,
            'predictedSales' => $predictedData,
            'predictedDates' => $predictedDates,
            'totalSales' => $totalSales,
            'currentPeriod' => $period, // Always 'monthly'
            'currentInventoryType' => $inventoryType,
            'inventoryBatches' => $inventoryBatches,
            'fastMovingProducts' => $fastMovingProducts,
        ]);
    }


    private function getInventoryBatches($inventoryType)
    {
        $inventoryQuery = Inventory::select('product_batches.id as batch_id', 'products.product_name', DB::raw('SUM(inventories.quantity) as quantity'))
            ->join('product_batches', 'inventories.batch_id', '=', 'product_batches.id')
            ->join('products', 'product_batches.product_id', '=', 'products.id')
            ->where('inventories.quantity', '>', 0)
            ->groupBy('product_batches.id', 'products.product_name');

        if ($inventoryType === 'highest') {
            $inventoryQuery->orderBy('quantity', 'desc');
        } else {
            $inventoryQuery->orderBy('quantity', 'asc');
        }

        $inventories = $inventoryQuery->limit(10)->get();

        return ProductBatch::whereIn('id', $inventories->pluck('batch_id'))
            ->with('product')
            ->get()
            ->map(function ($batch) use ($inventories) {
                $inventory = $inventories->firstWhere('batch_id', $batch->id);
                $batch->quantity = $inventory->quantity;
                $batch->product_name = $inventory->product_name;
                $batch->batch_id = $inventory->batch_id;  // Add batch_id here
                return $batch;
            });
    }


    private function getCounts()
    {
        $productCount = Product::count();
        $supplierCount = Supplier::count();
        $saleCount = Sale::count();

        return [
            'productCount' => $productCount,
            'supplierCount' => $supplierCount,
            'saleCount' => $saleCount,
        ];
    }


    private function getDateRangeAndFormats($period)
    {
        // Assuming the only valid period is 'monthly'
        if ($period === 'monthly') {
            $startDate = Carbon::now()->startOfMonth()->subMonths(4); // Start from 11 months ago
            $endDate = Carbon::now()->endOfMonth(); // End at the current month's end
            $groupBy = 'DATE_FORMAT(created_at, "%Y-%m")'; // Group by year and month
            $dateFormat = 'Y-m'; // Date format for grouping
            $displayFormat = 'M Y'; // Display format for presentation
        } else {
            // Optionally handle invalid periods or default behavior
            $startDate = Carbon::now()->startOfMonth()->subMonths(11);
            $endDate = Carbon::now()->endOfMonth();
            $groupBy = 'DATE_FORMAT(created_at, "%Y-%m")';
            $dateFormat = 'Y-m';
            $displayFormat = 'M Y';
        }

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'groupBy' => $groupBy,
            'dateFormat' => $dateFormat,
            'displayFormat' => $displayFormat,
        ];
    }



    public function predictSales()
    {
        // Retrieve historical sales data with more comprehensive retrieval
        $salesData = $this->getExtendedHistoricalData();

        // Prepare data for advanced prediction
        $processedData = $this->preprocessSalesData($salesData);

        // Handle cases with insufficient data
        if (count($processedData['samples']) < 5) {
            return $this->fallbackPrediction($processedData['targets']);
        }

        try {
            // Implement multiple prediction strategies
            $predictions = $this->generateMultiModelPredictions($processedData);

            // Store and return predictions
            return $this->storePredictionsAndPrepareResponse($predictions);
        } catch (\Exception $e) {
            // Fallback to simple prediction if advanced methods fail
            return $this->fallbackPrediction($processedData['targets'], $e->getMessage());
        }
    }

    private function getExtendedHistoricalData()
    {
        // Extended data retrieval with more context
        $historicalDataDays = Setting::where('key', 'historicalDataDays')->value('value') ?? 1800; // Increased from 900 to 1800 days
        $cutoffDate = now()->subDays($historicalDataDays);

        return DB::table('sales')
            ->whereDate('created_at', '>=', $cutoffDate)
            ->select(
                'created_at as date',
                'total_amount',
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('DAYOFWEEK(created_at) as day_of_week')
            )
            ->get()
            ->map(function ($record) {
                $record->date = Carbon::parse($record->date);
                return $record;
            });
    }

    private function preprocessSalesData($salesData)
    {
        $samples = [];
        $targets = [];
        $baseDate = $salesData->min('date');

        // More sophisticated feature engineering
        foreach ($salesData as $data) {
            if (!isset($data->date, $data->total_amount)) {
                continue;
            }

            $totalAmount = (float) $data->total_amount;
            $monthsOffset = $data->date->diffInMonths($baseDate);

            // Advanced feature vector
            $features = [
                'months_offset' => $monthsOffset,
                'month' => $data->month,
                'day_of_week' => $data->day_of_week,
                // Add more contextual features as needed
            ];

            $samples[] = $features;
            $targets[] = $totalAmount;
        }

        return [
            'samples' => $samples,
            'targets' => $targets,
            'base_date' => $baseDate
        ];
    }

    private function generateMultiModelPredictions($processedData)
    {
        $samples = $processedData['samples'];
        $targets = $processedData['targets'];
        $baseDate = $processedData['base_date'];

        // Ensemble of prediction methods
        $predictions = [
            'linear_regression' => $this->linearRegressionPrediction($samples, $targets),
            'moving_average' => $this->movingAveragePrediction($targets),
            'seasonal_adjustment' => $this->seasonalAdjustmentPrediction($samples, $targets)
        ];

        // Ensemble method: Weighted average
        $finalPredictions = array_map(function ($month) use ($predictions) {
            $monthPredictions = array_column($predictions, $month);
            return round(array_sum($monthPredictions) / count($monthPredictions));
        }, range(0, 2));

        return $finalPredictions;
    }

    private function linearRegressionPrediction($samples, $targets)
    {
        try {
            $regression = new LeastSquares();
            $regression->train($samples, $targets);

            $currentDate = strtotime('first day of next month');
            $baseDate = strtotime('first day of January 2020'); // Set a consistent base date

            return array_map(function ($i) use ($regression, $currentDate, $baseDate) {
                $monthsOffset = ($currentDate - $baseDate) / (30 * 24 * 60 * 60) + $i;
                $features = [
                    'months_offset' => $monthsOffset,
                    'month' => date('n', strtotime("+{$i} months", $currentDate)),
                    'day_of_week' => date('w', strtotime("+{$i} months", $currentDate))
                ];
                return max(0, round($regression->predict([$features])));
            }, range(0, 2));
        } catch (\Exception $e) {
            // Fallback if regression fails
            $lastValue = end($targets);
            return array_fill(0, 3, max(0, $lastValue));
        }
    }

    private function movingAveragePrediction($targets, $periods = 3)
    {
        // Calculate moving average of last n periods
        if (count($targets) < $periods) {
            $lastValue = end($targets);
            return array_fill(0, 3, max(0, $lastValue));
        }

        $slice = array_slice($targets, -$periods);
        $average = array_sum($slice) / count($slice);

        return array_fill(0, 3, round($average));
    }

    private function seasonalAdjustmentPrediction($samples, $targets)
    {
        // Basic seasonal adjustment method
        $monthlyAverages = collect($samples)
            ->groupBy('month')
            ->map(function ($group) use ($targets) {
                $indices = $group->keys();
                $monthTargets = collect($indices)->map(fn($i) => $targets[$i]);
                return $monthTargets->avg();
            });

        $lastMonths = collect($samples)
            ->sortByDesc('months_offset')
            ->take(3)
            ->pluck('month');

        return $lastMonths->map(function ($month) use ($monthlyAverages) {
            return round($monthlyAverages->get($month, 0));
        })->toArray();
    }

    private function storePredictionsAndPrepareResponse($predictions)
    {
        $currentDate = strtotime('first day of next month');

        $predictedSales = [];
        $predictedDates = [];

        foreach ($predictions as $index => $predictedValue) {
            $predictionDate = strtotime("+{$index} months", $currentDate);
            $formattedDate = date('F Y', $predictionDate);

            // Store prediction in database
            DB::table('sales_data')->updateOrInsert(
                ['key' => 'predicted_sales_' . date('Ym', $predictionDate)],
                [
                    'value' => $predictedValue,
                    'month' => $formattedDate,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            $predictedSales[] = $predictedValue;
            $predictedDates[] = $formattedDate;
        }

        return ['sales' => $predictedSales, 'dates' => $predictedDates];
    }

    private function fallbackPrediction($targets, $errorMessage = null)
    {
        $defaultSalesValue = !empty($targets) ? end($targets) : 0;

        $predictedSales = array_fill(0, 3, max(0, $defaultSalesValue));
        $predictedDates = array_map(
            fn($i) => date('F Y', strtotime("+{$i} months", strtotime('first day of next month'))),
            range(0, 2)
        );

        // Optional: Log the error or prediction fallback
        if ($errorMessage) {
            Log::warning('Sales Prediction Fallback: ' . $errorMessage);
        }

        return ['sales' => $predictedSales, 'dates' => $predictedDates];
    }
    private function getFastMovingProducts($startDate, $endDate)
    {
        return SaleDetail::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
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
                $totalSales = $saleDetail->total_quantity * $product->price; // Calculating the total sales amount

                return [
                    'product_id' => $product->id,
                    'product_name' => $product->product_name,
                    'generic_name' => $product->generic_name,
                    'category' => $product->category,
                    'total_quantity' => $saleDetail->total_quantity,
                    'price' => $product->price,
                    'total_sales' => $totalSales, // Added total sales
                ];
            });
    }
}
