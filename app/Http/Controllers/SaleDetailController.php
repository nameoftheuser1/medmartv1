<?php

namespace App\Http\Controllers;

use App\Models\SaleDetail;
use App\Models\Sale;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $saleDetails = SaleDetail::query()
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->select('sale_details.*')
            ->where(function ($query) use ($search) {
                $query->where('sales.id', 'like', "%{$search}%")
                    ->orWhere('products.product_name', 'like', "%{$search}%")
                    ->orWhere('sale_details.quantity', 'like', "%{$search}%")
                    ->orWhere('sale_details.price', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

         // Get today's sales details
         $salesPerDay = $this->getSalesDetailsToday();

         // Prepare data for ApexChart (Product Names and Total Amounts)
         $chartData = $salesPerDay->map(function ($sale) {
             return [
                 'product_name' => $sale->product_name,
                 'total_amount' => (float) $sale->total_amount,
             ];
         });

         // Pass data to the view
         return view('sale_details.index', [
             'saleDetails' => $saleDetails,
             'chartData' => $chartData,
         ]);
    }

    private function getSalesDetailsToday()
    {
        $today = Carbon::today();

        $saleDetails = SaleDetail::query()
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->select('products.product_name', DB::raw('SUM(sale_details.quantity * sale_details.price) as total_amount'))
            ->whereDate('sales.created_at', $today)  // Filter by today's date
            ->groupBy('sale_details.product_id', 'products.product_name')  // Group by product to get total sales per product
            ->orderByDesc('total_amount')  // Order by total amount
            ->limit(50)  // Get top 50 products
            ->get();

        return $saleDetails;
    }
}
