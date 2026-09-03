<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
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

        // Financial Data for Owner & Admin
        $financials = null;
        if ($user->isOwner() || $user->isAdmin()) {
            $financials = $this->financialService->getFinancialSummary();
        }

        // Real-time Attendance Stats
        $todayAttendanceQuery = Attendance::whereDate('waktu_presensi', today());
        $totalUsers = User::where('is_active', true)->count();
        $todayCheckedIn = (clone $todayAttendanceQuery)->where('tipe', 'masuk')->distinct('user_id')->count('user_id');
        $todayLate = (clone $todayAttendanceQuery)->where('tipe', 'masuk')->where('status', 'terlambat')->count();
        $todayAttendance = $todayAttendanceQuery->with('user')->latest('waktu_presensi')->get();

        // Content planner (status != published)
        $upcomingContents = ContentCalendar::with('assignee')
            ->where('status', '!=', 'published')
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        // Personal attendance for Karyawan / Siswa
        $personalAttendance = Attendance::where('user_id', $user->id)
            ->latest('waktu_presensi')
            ->take(10)
            ->get();

        // Recent Orders
        $recentOrders = Order::latest()->take(5)->get();

        // All users list for modal selections
        $usersList = User::where('is_active', true)->get();

        return view('dashboard', compact(
            'user',
            'financials',
            'todayAttendance',
            'totalUsers',
            'todayCheckedIn',
            'todayLate',
            'upcomingContents',
            'personalAttendance',
            'recentOrders',
            'usersList'
        ));
    }
}
