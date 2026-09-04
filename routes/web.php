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

        // Admin & Owner only
        Route::middleware(['role:owner,admin'])->group(function () {
            Route::post('/{attendance}/verify', [AttendanceController::class, 'verify'])->name('verify');
            Route::post('/update-office-location', [AttendanceController::class, 'updateOfficeLocation'])->name('update-office-location');
        });
    });

    // Modul 2: Pembukuan & Manajemen Arus Kas (Owner & Admin)
    Route::middleware(['role:owner,admin'])->group(function () {
        // Orders & Order Recap
        Route::get('orders/recap', [OrderController::class, 'recap'])->name('orders.recap');
        Route::get('orders/recap/export', [OrderController::class, 'exportRecap'])->name('orders.recap.export');
        Route::resource('orders', OrderController::class);

        // Cashflow & Finance
        Route::prefix('finance')->name('finance.')->group(function () {
            Route::get('/', [FinanceController::class, 'index'])->name('index');
            Route::post('/inflow', [FinanceController::class, 'storeInflow'])->name('inflow.store');
            Route::post('/outflow', [FinanceController::class, 'storeOutflow'])->name('outflow.store');
            Route::delete('/{cashflow}', [FinanceController::class, 'destroy'])->name('destroy');
        });

        // User Management
        Route::resource('users', UserController::class);
        Route::get('/users/{user}/register-face', [UserController::class, 'registerFaceView'])->name('users.register-face');
        Route::post('/users/{user}/register-face', [UserController::class, 'saveFace'])->name('users.save-face');
    });

    // Laba Rugi khusus Owner
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/finance/profit-loss', [FinanceController::class, 'profitLossReport'])->name('finance.profit_loss');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit_logs.index');
    });

    // Modul 3: Kalender Konten (Owner, Admin, Mentor)
    Route::middleware(['role:owner,admin,mentor'])->prefix('content')->name('content.')->group(function () {
        Route::get('/', [ContentCalendarController::class, 'index'])->name('index');
        Route::get('/events', [ContentCalendarController::class, 'events'])->name('events');
        Route::post('/', [ContentCalendarController::class, 'store'])->name('store');
        Route::put('/{contentCalendar}', [ContentCalendarController::class, 'update'])->name('update');
        Route::delete('/{contentCalendar}', [ContentCalendarController::class, 'destroy'])->name('destroy');
    });

});

require __DIR__.'/auth.php';
