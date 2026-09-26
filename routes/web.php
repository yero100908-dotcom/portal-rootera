<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ContentCalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    // Main Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Modul 1: Presensi Wajah (Face Recognition Attendance)
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/scan', [AttendanceController::class, 'create'])->name('create');
        Route::post('/scan', [AttendanceController::class, 'store'])->name('store');
        Route::get('/export', [AttendanceController::class, 'export'])->name('export');

        // Admin, Owner & IT Verification
        Route::middleware(['role:owner,admin,it'])->group(function () {
            Route::post('/{attendance}/verify', [AttendanceController::class, 'verify'])->name('verify');
            Route::post('/update-office-location', [AttendanceController::class, 'updateOfficeLocation'])->name('update-office-location');
        });
    });

    // Modul 2: Order Book & Rekap Order (Owner, Admin, CS, Mentor)
    Route::middleware(['role:owner,admin,cs,mentor'])->group(function () {
        Route::get('orders/recap', [OrderController::class, 'recap'])->name('orders.recap');
        Route::get('orders/recap/export', [OrderController::class, 'exportRecap'])->name('orders.recap.export');
        Route::resource('orders', OrderController::class);
    });

    // Modul 3: Arus Kas / Finance (Owner & Admin)
    Route::middleware(['role:owner,admin'])->prefix('finance')->name('finance.')->group(function () {
        Route::get('/', [FinanceController::class, 'index'])->name('index');
        Route::post('/inflow', [FinanceController::class, 'storeInflow'])->name('inflow.store');
        Route::post('/outflow', [FinanceController::class, 'storeOutflow'])->name('outflow.store');
        Route::delete('/{cashflow}', [FinanceController::class, 'destroy'])->name('destroy');
    });

    // Modul 4: User Management & Data Wajah (Owner, Admin, IT)
    Route::middleware(['role:owner,admin,it'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/users/{user}/register-face', [UserController::class, 'registerFaceView'])->name('users.register-face');
        Route::post('/users/{user}/register-face', [UserController::class, 'saveFace'])->name('users.save-face');
    });

    // Modul 5: Audit Trail Logs (Owner, IT)
    Route::middleware(['role:owner,it'])->group(function () {
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit_logs.index');
    });

    // Modul 6: Laba Rugi khusus Owner
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/finance/profit-loss', [FinanceController::class, 'profitLossReport'])->name('finance.profit_loss');
    });

    // Modul 7: Kalender Konten / Media Planner (Owner, Admin, Mentor, Sosmed)
    Route::middleware(['role:owner,admin,mentor,sosmed'])->prefix('content')->name('content.')->group(function () {
        Route::get('/', [ContentCalendarController::class, 'index'])->name('index');
        Route::get('/events', [ContentCalendarController::class, 'events'])->name('events');
        Route::post('/', [ContentCalendarController::class, 'store'])->name('store');
        Route::put('/{contentCalendar}', [ContentCalendarController::class, 'update'])->name('update');
        Route::delete('/{contentCalendar}', [ContentCalendarController::class, 'destroy'])->name('destroy');
    });

});

require __DIR__.'/auth.php';
