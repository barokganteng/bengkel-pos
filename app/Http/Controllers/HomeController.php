<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use App\Models\Sparepart;
use Carbon\Carbon;
use App\Models\ServiceHistory;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // 1. Ambil Jumlah Pelanggan
        $totalCustomers = User::customer()->count();

        // 2. Ambil Pendapatan Hari Ini
        $todaysRevenue = ServiceHistory::whereDate('service_date', Carbon::today())->sum('total_price');

        // 3. Ambil Booking Pending
        $pendingBookings = Booking::where('status', 'pending')->count();

        // 4. Ambil Stok Menipis (kita asumsikan < 5)
        $lowStockItems = Sparepart::where('stock', '<', 5)->count();

        // Kirim semua data ke view
        return view('home', compact(
            'totalCustomers',
            'todaysRevenue',
            'pendingBookings',
            'lowStockItems'
        ));
    }
}
