<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\StockIn;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Room;
use App\Models\StockOut;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter');

        $query = Report::with('user')->orderBy('created_at', 'desc');

        if ($filter == 'items') {
            $query->where('activity', 'like', 'Added new item:%')
                ->orWhere('activity', 'like', 'Updated item:%')
                ->orWhere('activity', 'like', 'Deleted item:%');
        } elseif ($filter == 'suppliers') {
            $query->where('activity', 'like', 'Added new supplier:%')
                ->orWhere('activity', 'like', 'Updated supplier:%')
                ->orWhere('activity', 'like', 'Deleted supplier:%');
        } elseif ($filter == 'stock_in') {
            $query->where('activity', 'like', 'Added Stock-In record%')
                ->orWhere('activity', 'like', 'Updated Stock-In record%')
                ->orWhere('activity', 'like', 'Deleted Stock-In record%');
        } elseif ($filter == 'stock_out') {
            $query->where('activity', 'like', 'Stocked out item:%');
        }

        $reports = $query->paginate(10);

        $totalStockInThisMonth = StockIn::whereMonth('stockin_date', now()->month)
            ->whereYear('stockin_date', now()->year)
            ->count();

        $totalStockOutThisMonth = Report::where('activity', 'like', 'Stocked out item:%')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $totalSuppliers = Supplier::count();
        $totalItems = Item::count();

        return view('reports.index', compact('reports', 'totalStockInThisMonth', 'totalStockOutThisMonth', 'totalSuppliers', 'totalItems'));
    }
}