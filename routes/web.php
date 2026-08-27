<?php

use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UpsellingItemController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RevenueTargetController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Route::get('/', function () {
//     if (!Auth::check()) {
//         return redirect()->route('login');
//     }

//     $user = Auth::user();

//     if ($user->hasAnyRole([
//         'Super Admin', 'Restaurant Manager', 'Assistant Restaurant Manager', 
//         'F&B Supervisor', 'Waiter', 'Cashier', 'Bartender', 'Daily Worker', 'Trainee'
//     ])) {
//         return redirect()->route('dashboard');
//     }

//     abort(403, 'Unauthorized. You do not have access to this App.');
// });
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'redirect'])->name('login');
Route::get('/auth/redirect', [AuthController::class, 'redirect']);
Route::get('/auth/callback', [AuthController::class, 'callback']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'can:access-checklist-app'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/analytics/{restaurant}', [DashboardController::class, 'getOutletAnalytics'])->name('dashboard.analytics');

    Route::get('/daily-reports', [DailyReportController::class, 'index'])->name('daily-reports.index');
    Route::get('/daily-reports/create', [DailyReportController::class, 'create'])->name('daily-reports.create');
    Route::post('/daily-reports/export', [DailyReportController::class, 'exportExcel'])->name('daily-reports.export');
    Route::post('/daily-reports/export-pdf', [DailyReportController::class, 'exportPdfBulk'])->name('daily-reports.export-pdf');
    Route::post('/daily-reports', [DailyReportController::class, 'store'])->name('daily-reports.store');
    Route::get('/daily-reports/{dailyReport}/edit', [DailyReportController::class, 'edit'])->name('daily-reports.edit');
    Route::put('/daily-reports/{dailyReport}', [DailyReportController::class, 'update'])->name('daily-reports.update');
    Route::get('/daily-reports/{dailyReport}', [DailyReportController::class, 'show'])->name('daily-reports.show');
    Route::get('/daily-reports/{dailyReport}/pdf', [DailyReportController::class, 'downloadPdf'])->name('daily-reports.pdf');
    Route::patch('/daily-reports/{dailyReport}/approve', [DailyReportController::class, 'approve'])->name('daily-reports.approve');
    Route::patch('/daily-reports/{dailyReport}/reject', [DailyReportController::class, 'reject'])->name('daily-reports.reject');
    Route::delete('/daily-reports/{dailyReport}', [DailyReportController::class, 'destroy'])->name('daily-reports.destroy');


    Route::middleware(['role:Super Admin'])->group(function () {
        
        Route::get('/upselling-items', [UpsellingItemController::class, 'index'])->name('upselling-items.index');
        Route::get('/upselling-items/create', [UpsellingItemController::class, 'create'])->name('upselling-items.create');
        Route::post('/upselling-items', [UpsellingItemController::class, 'store'])->name('upselling-items.store');
        Route::get('/upselling-items/{upsellingItem}/edit', [UpsellingItemController::class, 'edit'])->name('upselling-items.edit');
        Route::put('/upselling-items/{upsellingItem}', [UpsellingItemController::class, 'update'])->name('upselling-items.update');
        Route::delete('/upselling-items/{upsellingItem}', [UpsellingItemController::class, 'destroy'])->name('upselling-items.destroy');

        Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
        Route::get('/restaurants/create', [RestaurantController::class, 'create'])->name('restaurants.create');
        Route::post('/restaurants', [RestaurantController::class, 'store'])->name('restaurants.store');
        Route::get('/restaurants/{restaurant}/edit', [RestaurantController::class, 'edit'])->name('restaurants.edit');
        Route::put('/restaurants/{restaurant}', [RestaurantController::class, 'update'])->name('restaurants.update');
        Route::delete('/restaurants/{restaurant}', [RestaurantController::class, 'destroy'])->name('restaurants.destroy');

        Route::resource('employees', \App\Http\Controllers\EmployeeController::class);

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });


    Route::middleware(['role:Super Admin|Restaurant Manager'])->group(function () {
        Route::get('/revenue-targets', [RevenueTargetController::class, 'index'])->name('revenue-targets.index');
        Route::post('/revenue-targets', [RevenueTargetController::class, 'store'])->name('revenue-targets.store');
    });

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// require __DIR__ . '/auth.php';
