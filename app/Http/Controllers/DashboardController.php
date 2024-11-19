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
        $period = $request->input('period', 'weekly');
        $inventoryType = $request->input('inventory-type', 'highest');

        $dateSettings = $this->getDateRangeAndFormats($period);
        $startDate = $dateSettings['startDate'];
        $endDate = $dateSettings['endDate'];
        $groupBy = $dateSettings['groupBy'];
        $dateFormat = $dateSettings['dateFormat'];
        $displayFormat = $dateSettings['displayFormat'];

        $salesData = Sale::select(DB::raw("$groupBy as date"), DB::raw('SUM(total_amount) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        $categories = [];
        $salesSeries = [];

        $totalSales = DB::table('sales')
            ->where('status', 'complete')
            ->sum('total_amount');

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $formattedDate = $currentDate->format($dateFormat);
            $categories[] = $currentDate->format($displayFormat);
            $dailySales = $salesData->get($formattedDate)->total ?? 0;
            $salesSeries[] = $dailySales;

            if ($period === 'weekly') {
                $currentDate->addDay();
            } elseif ($period === 'monthly') {
                $currentDate->addMonth();
            } else {
                $currentDate->addYear();
            }
        }

        //for the counts
        $counts = $this->getCounts();

        $productCount = $counts['productCount'];
        $supplierCount = $counts['supplierCount'];
        $saleCount = $counts['saleCount'];

        // This code retrieves product batches that are expiring within the next 30 days
        $thresholdDate = Carbon::now()->addDays(30);

        $expiringBatches = ProductBatch::with(['product', 'inventories'])
            ->where('expiration_date', '<=', $thresholdDate)
            ->whereHas('inventories', function ($query) {
                $query->where('quantity', '>', 0);
            })
            ->orderBy('expiration_date', 'asc')
            ->paginate(5);

        //this get the sales today
        $totalSalesToday = Sale::whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        //this is for the fast moving product
        $fastMovingProducts = $this->getFastMovingProducts($startDate, $endDate);


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

        $prediction = $this->predictSales();
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
            // Fallback to default prediction
            $defaultSalesValue = !empty($targets) ? end($targets) : 0;

            $predictedSales = array_fill(0, 10, max(0, $defaultSalesValue));
            $predictedDates = array_map(
                fn($i) => date('F Y', strtotime("+{$i} months")),
                range(0, 9)
            );

            return ['sales' => $predictedSales, 'dates' => $predictedDates];
        }

        // Perform linear regression with Phpml
        try {
            $regression = new LeastSquares();
            $regression->train($samples, $targets);

            // Generate predictions for the next 10 months
            $predictedSales = [];
            $predictedDates = [];
            $currentDate = strtotime('first day of next month');
            $now = strtotime('first day of this month');

            for ($i = 0; $i < 10; $i++) {
                // Normalize current date as months offset from base date
                $currentOffset = ($currentDate - $baseDate) / (30 * 24 * 60 * 60);

                $predictedValue = max(0, round($regression->predict([$currentOffset])));
                $predictedDate = date('F Y', $currentDate);

                // Store the prediction in the database if the month is in the past
                if ($currentDate < $now) {
                    DB::table('sales_data')->updateOrInsert(
                        ['key' => 'predicted_sales_' . date('Ym', $currentDate)],
                        [
                            'value' => $predictedValue,
                            'month' => $predictedDate,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    );
                }

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

            $predictedSales = array_fill(0, 10, max(0, $defaultSalesValue));
            $predictedDates = array_map(
                fn($i) => date('F Y', strtotime("+{$i} months")),
                range(0, 9)
            );

            return ['sales' => $predictedSales, 'dates' => $predictedDates];
        }
    }


    private function getHistoricalData()
    {
        $historicalDataDays = Setting::where('key', 'historicalDataDays')->value('value') ?? 90;

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
                return [
                    'product_id' => $product->id,
                    'product_name' => $product->product_name,
                    'generic_name' => $product->generic_name,
                    'category' => $product->category,
                    'total_quantity' => $saleDetail->total_quantity,
                    'price' => $product->price,
                ];
            });
    }
}
