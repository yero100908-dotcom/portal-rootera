<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Cashflow;
use App\Models\ContentCalendar;
use App\Models\Order;
use App\Models\User;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected FinancialService $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function index()
    {
        $user = Auth::user();

        // Data for Owner & Admin
        $financials = null;
        if ($user->isOwner() || $user->isAdmin()) {
            $financials = $this->financialService->getFinancialSummary();
        }

        // Attendance stats
        $todayAttendance = Attendance::whereDate('waktu_presensi', today())->get();
        $totalUsers = User::where('is_active', true)->count();
        $todayCheckedIn = $todayAttendance->where('tipe', 'masuk')->count();
        $todayLate = $todayAttendance->where('tipe', 'masuk')->where('status', 'terlambat')->count();

        // Content planner stats
        $upcomingContents = ContentCalendar::where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        // Personal attendance for Karyawan / Siswa
        $personalAttendance = Attendance::where('user_id', $user->id)
            ->latest('waktu_presensi')
            ->take(10)
            ->get();

        $recentOrders = Order::latest()->take(5)->get();

        return view('dashboard', compact(
            'user',
            'financials',
            'todayAttendance',
            'totalUsers',
            'todayCheckedIn',
            'todayLate',
            'upcomingContents',
            'personalAttendance',
            'recentOrders'
        ));
    }
}
