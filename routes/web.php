<?php

use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UpsellingItemController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RevenueTargetController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Route::get('/', function () {
//     return view('auth.login');
// });

Route::get('/', function () {
    // Jika belum login, lempar ke route 'login' (yang akan redirect ke Portal)
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    // LOGIKA REDIRECT BERDASARKAN ROLE (Pindahan dari AuthenticatedSessionController)
    $user = Auth::user();

    // Gunakan helper 'hasAnyRole' dari jembatan User.php -> LocalUser
    if (
        $user->hasRole('Super Admin') ||
        $user->hasRole('Restaurant Manager') ||
        $user->hasRole('Assistant Restaurant Manager') ||
        $user->hasRole('F&B Supervisor') ||
        $user->hasRole('Waiter') ||
        $user->hasRole('Cashier') ||
        $user->hasRole('Bartender') ||
        $user->hasRole('Daily Worker') ||
        $user->hasRole('Trainee')
    ) {
        return redirect()->route('dashboard');
    }

    // Default untuk Staff
    abort(403, 'Unauthorized. You do not have access to Uniform App.');
});

Route::get('/login', function () {
    // $portalUrl = env('APP_PORTAL_URL', 'http://portal.sso.test');

    // PERBAIKAN: Gunakan url('/') agar kembali ke halaman utama (root), bukan ke /login lagi.
    return redirect(env('APP_PORTAL_URL') . '/login?redirect_to=' . url('/'));
})->name('login');

Route::middleware(['auth', 'verified', 'global.checklist', 'can:access-checklist-app'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/analytics/{restaurant}', [DashboardController::class, 'getOutletAnalytics'])->name('dashboard.analytics');

    Route::get('/daily-reports', [DailyReportController::class, 'index'])->name('daily-reports.index');
    Route::get('/daily-reports/create', [DailyReportController::class, 'create'])->name('daily-reports.create');
    Route::post('/daily-reports', [DailyReportController::class, 'store'])->name('daily-reports.store');
    Route::get('/daily-reports/{dailyReport}/edit', [DailyReportController::class, 'edit'])->name('daily-reports.edit');
    Route::put('/daily-reports/{dailyReport}', [DailyReportController::class, 'update'])->name('daily-reports.update');
    Route::get('/daily-reports/{dailyReport}', [DailyReportController::class, 'show'])->name('daily-reports.show');
    Route::get('/daily-reports/{dailyReport}/pdf', [DailyReportController::class, 'downloadPdf'])->name('daily-reports.pdf');
    Route::patch('/daily-reports/{dailyReport}/approve', [DailyReportController::class, 'approve'])->name('daily-reports.approve');
    Route::patch('/daily-reports/{dailyReport}/reject', [DailyReportController::class, 'reject'])->name('daily-reports.reject');
    Route::delete('/daily-reports/{dailyReport}', [DailyReportController::class, 'destroy'])->name('daily-reports.destroy');

    Route::get('/upselling-items', [UpsellingItemController::class, 'index'])->middleware(['can:is-super-admin'])->name('upselling-items.index');
    Route::get('/upselling-items/create', [UpsellingItemController::class, 'create'])->middleware(['can:is-super-admin'])->name('upselling-items.create');
    Route::post('/upselling-items', [UpsellingItemController::class, 'store'])->middleware(['can:is-super-admin'])->name('upselling-items.store');
    Route::get('/upselling-items/{upsellingItem}/edit', [UpsellingItemController::class, 'edit'])->middleware(['can:is-super-admin'])->name('upselling-items.edit');
    Route::put('/upselling-items/{upsellingItem}', [UpsellingItemController::class, 'update'])->middleware(['can:is-super-admin'])->name('upselling-items.update');
    Route::delete('/upselling-items/{upsellingItem}', [UpsellingItemController::class, 'destroy'])->middleware(['can:is-super-admin'])->name('upselling-items.destroy');

    Route::get('/restaurants', [RestaurantController::class, 'index'])->middleware(['can:is-super-admin'])->name('restaurants.index');
    Route::get('/restaurants/create', [RestaurantController::class, 'create'])->middleware(['can:is-super-admin'])->name('restaurants.create');
    Route::post('/restaurants', [RestaurantController::class, 'store'])->middleware(['can:is-super-admin'])->name('restaurants.store');
    Route::get('/restaurants/{restaurant}/edit', [RestaurantController::class, 'edit'])->middleware(['can:is-super-admin'])->name('restaurants.edit');
    Route::put('/restaurants/{restaurant}', [RestaurantController::class, 'update'])->middleware(['can:is-super-admin'])->name('restaurants.update');
    Route::delete('/restaurants/{restaurant}', [RestaurantController::class, 'destroy'])->middleware(['can:is-super-admin'])->name('restaurants.destroy');

    Route::get('/users', [UserController::class, 'index'])->middleware(['can:is-super-admin'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->middleware(['can:is-super-admin'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->middleware(['can:is-super-admin'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->middleware(['can:is-super-admin'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware(['can:is-super-admin'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware(['can:is-super-admin'])->name('users.destroy');

    Route::get('/revenue-targets', [RevenueTargetController::class, 'index'])->middleware(['can:can-revenue-targets'])->name('revenue-targets.index');
    Route::post('/revenue-targets', [RevenueTargetController::class, 'store'])->middleware(['can:can-revenue-targets'])->name('revenue-targets.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::match(['get', 'post'], 'logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect(env('APP_PORTAL_URL') . '/logout');
})->name('logout');

// require __DIR__ . '/auth.php';
