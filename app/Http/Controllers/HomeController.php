<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use App\Models\Sparepart;
use Carbon\Carbon;
use App\Models\Service;
use App\Models\ServiceHistory;
use App\Models\ServiceDetail;
use Illuminate\Support\Facades\DB;


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
        // --- 1. DATA UNTUK STAT CARDS (Tidak berubah) ---
        $totalCustomers = User::customer()->count();
        $todaysRevenue = ServiceHistory::whereDate('service_date', Carbon::today())->sum('total_price');
        $pendingBookings = Booking::where('status', 'pending')->count();
        $lowStockItems = Sparepart::where('stock', '<', 5)->count();

        // --- 2. DATA UNTUK GRAFIK PENDAPATAN 7 HARI (Tidak berubah) ---
        $revenueLabels = [];
        $revenueData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenueLabels[] = $date->format('D, j M');
            $revenueData[] = ServiceHistory::whereDate('service_date', $date)->sum('total_price');
        }

        // --- 3. DATA UNTUK GRAFIK JASA TERLARIS (PERBAIKAN) ---

        // Kita gunakan JOIN untuk mengambil nama service
        $topServices = ServiceDetail::where('itemable_type', Service::class)
            ->join('services', 'service_details.itemable_id', '=', 'services.id')
            ->select('services.name', DB::raw('COUNT(service_details.id) as count'))
            ->groupBy('services.name')
            ->orderBy('count', 'DESC')
            ->take(5)
            ->get();

        $topServiceLabels = $topServices->pluck('name');
        $topServiceData = $topServices->pluck('count');

        // --- 4. DATA UNTUK RIWAYAT TRANSAKSI TERBARU (BARU) ---
        $recentTransactions = ServiceHistory::with(['customer', 'vehicle'])
            ->latest() // Ini = orderBy('created_at', 'DESC')
            ->take(5)  // Ambil 5 transaksi terbaru
            ->get();

        // --- 5. KIRIM SEMUA DATA KE VIEW ---
        return view('home', compact(
            'totalCustomers',
            'todaysRevenue',
            'pendingBookings',
            'lowStockItems',
            'revenueLabels',
            'revenueData',
            'topServiceLabels',
            'topServiceData',
            'recentTransactions',
        ));
    }
}
