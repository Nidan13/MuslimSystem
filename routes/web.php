<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\QuestController;
use App\Http\Controllers\Admin\DungeonController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\QuestTypeController;
use App\Http\Controllers\Admin\RankTierController;
use App\Http\Controllers\Admin\DungeonTypeController;
use App\Http\Controllers\Admin\LevelConfigController;
use App\Http\Controllers\Admin\DailyTaskController as AdminDailyTaskController;
use App\Http\Controllers\Admin\CircleController;
use App\Http\Controllers\Admin\PrayerLogController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\PrayerController as AdminMasterPrayerController;
use App\Http\Controllers\Admin\IslamicVideoController;
use App\Http\Controllers\Admin\AffiliateController;
use App\Http\Controllers\Admin\WithdrawalController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::get('/', fn() => redirect('/login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // 1. Redirect Central: Menentukan arah berdasarkan rank saat akses /dashboard
    Route::get('/dashboard', function() {
        $user = auth()->user()->load('rankTier');
        // Logika: Hanya ID 1 atau Rank S yang ke Admin
        if ($user->id === 1 || (optional($user->rankTier)->slug === 'S')) {
            return redirect()->route('admin.dashboard');
        }
        return response()->json(['message' => 'Please use the mobile app.']);
    })->name('dashboard');

    // 2. Admin Group
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('quests', QuestController::class);
        Route::resource('dungeons', DungeonController::class);
        Route::resource('shop', ShopController::class);
        Route::resource('hunters', UserController::class);
        Route::resource('quest-types', QuestTypeController::class);
        Route::resource('rank-tiers', RankTierController::class);
        Route::resource('dungeon-types', DungeonTypeController::class);
        Route::resource('level-configs', LevelConfigController::class)->parameters([
            'level-configs' => 'level_config'
        ]);
        
        Route::resource('circles', CircleController::class);
        Route::resource('islamic-videos', IslamicVideoController::class);
    
        // Daily Tasks (Master)
        Route::resource('daily-tasks', AdminDailyTaskController::class);
        Route::get('/users-with-custom-tasks', [AdminDailyTaskController::class, 'usersWithCustomTasks'])->name('daily-tasks.users');
        Route::get('/users/{user}/custom-tasks', [AdminDailyTaskController::class, 'userCustomTasks'])->name('daily-tasks.user-tasks');

        // Logs & Journal
        Route::resource('prayer-logs', PrayerLogController::class)->only(['index', 'destroy']);
        Route::resource('activity-logs', ActivityLogController::class)->only(['index', 'destroy']);

        // Affiliate & Commissions
        Route::get('/affiliates', [AffiliateController::class, 'index'])->name('affiliates.index');
        Route::get('/affiliates/{user}', [AffiliateController::class, 'show'])->name('affiliates.show');
        Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::patch('/withdrawals/{withdrawal}', [WithdrawalController::class, 'update'])->name('withdrawals.update');

        // Master Data Sholat
        Route::resource('prayers', AdminMasterPrayerController::class)->except(['create', 'store', 'show', 'destroy']);
    });
});
