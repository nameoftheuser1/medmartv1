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
        // Retrieve historical sales data
        $salesData = $this->getHistoricalData();

        $samples = [];
        $targets = [];

        // Base date for normalizing
        $baseDate = null;

        foreach ($salesData as $data) {
            if (!isset($data->date, $data->total_amount)) {
                continue;
            }

            $totalAmount = (float) $data->total_amount;
            $dataDate = strtotime($data->date);

            // Initialize base date for normalization
            if ($baseDate === null) {
                $baseDate = $dataDate;
            }

            // Normalize date as months offset
            $monthsOffset = ($dataDate - $baseDate) / (30 * 24 * 60 * 60);

            $samples[] = [$monthsOffset];
            $targets[] = $totalAmount;
        }

        // Handle cases with insufficient unique data for predictions
        $uniqueSamples = array_unique(array_map('serialize', $samples));
        if (count($uniqueSamples) < 2) {
            // Fallback to default prediction (3 months)
            $defaultSalesValue = !empty($targets) ? end($targets) : 0;

            $predictedSales = array_fill(0, 3, max(0, $defaultSalesValue)); // 3 months prediction
            $predictedDates = array_map(
                fn($i) => date('F Y', strtotime("+{$i} months", strtotime('first day of next month'))),
                range(0, 2) // Predict for 3 months (0 to 2)
            );

            return ['sales' => $predictedSales, 'dates' => $predictedDates];
        }

        // Perform linear regression with Phpml
        try {
            $regression = new LeastSquares();
            $regression->train($samples, $targets);

            // Generate predictions for the next 3 months
            $predictedSales = [];
            $predictedDates = [];
            $currentDate = strtotime('first day of next month'); // Start from next month

            for ($i = 0; $i < 3; $i++) { // Predict for exactly 3 months
                // Normalize current date as months offset from base date
                $currentOffset = ($currentDate - $baseDate) / (30 * 24 * 60 * 60);

                $predictedValue = max(0, round($regression->predict([$currentOffset])));
                $predictedDate = date('F Y', $currentDate);

                // Store the prediction in the database if it's for a future month
                DB::table('sales_data')->updateOrInsert(
                    ['key' => 'predicted_sales_' . date('Ym', $currentDate)],
                    [
                        'value' => $predictedValue,
                        'month' => $predictedDate,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );

                // Store prediction for return
                $predictedSales[] = $predictedValue;
                $predictedDates[] = $predictedDate;

                // Move to the next prediction month
                $currentDate = strtotime("+1 month", $currentDate);
            }

            return ['sales' => $predictedSales, 'dates' => $predictedDates];
        } catch (\Phpml\Exception\MatrixException $e) {
            // Handle regression failure with a fallback
            $defaultSalesValue = !empty($targets) ? end($targets) : 0;

            $predictedSales = array_fill(0, 3, max(0, $defaultSalesValue)); // 3 months prediction
            $predictedDates = array_map(
                fn($i) => date('F Y', strtotime("+{$i} months", strtotime('first day of next month'))),
                range(0, 2) // Predict for 3 months (0 to 2)
            );

            return ['sales' => $predictedSales, 'dates' => $predictedDates];
        }
    }

    private function getHistoricalData()
    {
        $historicalDataDays = Setting::where('key', 'historicalDataDays')->value('value') ?? 900;

        $cutoffDate = now()->subDays($historicalDataDays);

        $salesData = DB::table('sales')
            ->whereDate('created_at', '>=', $cutoffDate)
            ->select('created_at as date', 'total_amount')
            ->get()
            ->map(function ($record) {
                $record->date = Carbon::parse($record->date)->format('Y-m-d');
                return $record;
            });

        return $salesData;
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
