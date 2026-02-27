<?php

use App\Http\Controllers\User\DailyTaskController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\TodoController;
use App\Http\Controllers\User\TemplateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes (no authentication required)
Route::post('/prismalink/webhook', [\App\Http\Controllers\User\PaymentController::class, 'webhook']);
Route::get('/payments/callback', [\App\Http\Controllers\User\PaymentController::class, 'callback']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/google', [AuthController::class, 'googleLogin']);
Route::get('/islamic-videos', [\App\Http\Controllers\User\IslamicVideoController::class, 'index']);

// Protected routes (require authentication)
    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        
        // Auth routes
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/toggle-menstruation', [AuthController::class, 'toggleMenstruation']);
        
        // Payments & Profile (Allowed even if inactive)
        Route::get('/profile', [\App\Http\Controllers\User\ProfileController::class, 'index']);
        Route::get('/profile/activities', [\App\Http\Controllers\User\ProfileController::class, 'activities']);
        Route::get('/profile/{id}', [\App\Http\Controllers\User\ProfileController::class, 'show']);
        Route::post('/profile/update', [\App\Http\Controllers\User\ProfileController::class, 'update']);
        Route::post('/profile/change-password', [\App\Http\Controllers\User\ProfileController::class, 'changePassword']);
        Route::post('/payments/create-link', [\App\Http\Controllers\User\PaymentController::class, 'createLink']);
        Route::get('/payments/status', [\App\Http\Controllers\User\PaymentController::class, 'status']);
        Route::post('/payments/inquiry', [\App\Http\Controllers\User\PaymentController::class, 'inquiry']);

        // Routes requiring ACTIVE account
        Route::middleware(\App\Http\Middleware\ActiveUser::class)->group(function () {
            
            // Home Aggregation
            Route::get('/home', [\App\Http\Controllers\User\HomeController::class, 'index']);

            Route::get('/profile/debug', [\App\Http\Controllers\User\ProfileController::class, 'debug']);
            Route::post('/profile/penalty', [\App\Http\Controllers\User\ProfileController::class, 'penalty']);
            Route::post('/profile/history-check', [\App\Http\Controllers\User\ProfileController::class, 'historyCheck']);
            
            // Quran Progress
            Route::get('/quran/progress', [\App\Http\Controllers\User\QuranController::class, 'index']);
            Route::post('/quran/progress/toggle', [\App\Http\Controllers\User\QuranController::class, 'toggle']);

            // Daily Tasks API for Flutter
            Route::prefix('daily-tasks')->group(function () {
                Route::get('/', [DailyTaskController::class, 'index']);
                Route::post('/', [DailyTaskController::class, 'store']);
                Route::put('/{id}', [DailyTaskController::class, 'update']);
                Route::delete('/{id}', [DailyTaskController::class, 'destroy']);
                Route::post('/{id}/complete', [DailyTaskController::class, 'complete']);
                Route::post('/{id}/uncomplete', [DailyTaskController::class, 'uncomplete']);
            });

            Route::prefix('prayers')->group(function () {
                Route::get('/', [\App\Http\Controllers\User\PrayerController::class, 'index']);
                Route::post('/sync-schedule', [\App\Http\Controllers\User\PrayerController::class, 'syncSchedule']);
                Route::post('/complete', [\App\Http\Controllers\User\PrayerController::class, 'complete']);
                Route::post('/uncomplete', [\App\Http\Controllers\User\PrayerController::class, 'uncomplete']);
            });

            // Quests API
            Route::prefix('quests')->group(function () {
                Route::get('/', [\App\Http\Controllers\User\QuestController::class, 'index']);
                Route::get('/{id}', [\App\Http\Controllers\User\QuestController::class, 'show']);
                Route::post('/{id}/accept', [\App\Http\Controllers\User\QuestController::class, 'accept']);
                Route::post('/{id}/progress', [\App\Http\Controllers\User\QuestController::class, 'updateProgress']);
                Route::post('/{id}/complete', [\App\Http\Controllers\User\QuestController::class, 'complete']);
                Route::delete('/{id}', [\App\Http\Controllers\User\QuestController::class, 'destroy']);
            });

            // Habits API
            Route::prefix('habits')->group(function () {
                Route::get('/', [\App\Http\Controllers\User\HabitController::class, 'index']);
                Route::post('/', [\App\Http\Controllers\User\HabitController::class, 'store']);
                Route::post('/{id}/score', [\App\Http\Controllers\User\HabitController::class, 'score']);
                Route::delete('/{id}', [\App\Http\Controllers\User\HabitController::class, 'destroy']);
            });

            // To-Do List API
            Route::prefix('todos')->group(function () {
                Route::get('/', [\App\Http\Controllers\User\TodoController::class, 'index']);
                Route::post('/', [\App\Http\Controllers\User\TodoController::class, 'store']);
                Route::put('/{id}', [\App\Http\Controllers\User\TodoController::class, 'update']);
                Route::post('/{id}/complete', [\App\Http\Controllers\User\TodoController::class, 'complete']);
                Route::delete('/{id}', [\App\Http\Controllers\User\TodoController::class, 'destroy']);
            });

            // Circles API
            Route::prefix('circles')->group(function () {
                Route::get('/', [\App\Http\Controllers\User\CircleController::class, 'index']);
                Route::get('/my', [\App\Http\Controllers\User\CircleController::class, 'myCircles']);
                Route::get('/search-users', [\App\Http\Controllers\User\CircleController::class, 'searchUsers']);
                Route::get('/{circle}', [\App\Http\Controllers\User\CircleController::class, 'show']);
                Route::post('/', [\App\Http\Controllers\User\CircleController::class, 'store']);
                Route::post('/{circle}/join', [\App\Http\Controllers\User\CircleController::class, 'join']);
                Route::post('/{circle}/leave', [\App\Http\Controllers\User\CircleController::class, 'leave']);
                Route::get('/{circle}/raids', [\App\Http\Controllers\User\RaidController::class, 'index']);
                Route::post('/{circle}/raids/{dungeon}/join', [\App\Http\Controllers\User\RaidController::class, 'joinLobby']);
                Route::post('/{circle}/promote', [\App\Http\Controllers\User\CircleController::class, 'promote']);
            });

            // Leaderboard API
            Route::prefix('leaderboard')->group(function () {
                Route::get('/users', [\App\Http\Controllers\User\LeaderboardController::class, 'users']);
                Route::get('/circles', [\App\Http\Controllers\User\LeaderboardController::class, 'circles']);
            });

            // Social API
            Route::prefix('social')->group(function () {
                Route::get('/search', [\App\Http\Controllers\User\SocialController::class, 'search']);
                Route::post('/follow/{id}', [\App\Http\Controllers\User\SocialController::class, 'follow']);
                Route::post('/unfollow/{id}', [\App\Http\Controllers\User\SocialController::class, 'unfollow']);
                Route::get('/{id}/followers', [\App\Http\Controllers\User\SocialController::class, 'followers']);
                Route::get('/{id}/following', [\App\Http\Controllers\User\SocialController::class, 'following']);
            });

            // Affiliate API
            Route::prefix('affiliate')->group(function () {
                Route::get('/stats', [\App\Http\Controllers\User\AffiliateController::class, 'stats']);
                Route::get('/commissions', [\App\Http\Controllers\User\AffiliateController::class, 'commissions']);
                Route::post('/withdraw', [\App\Http\Controllers\User\AffiliateController::class, 'withdraw']);
                Route::get('/withdrawals', [\App\Http\Controllers\User\AffiliateController::class, 'withdrawals']);
            });

            // Notifications API
            Route::prefix('notifications')->group(function () {
                Route::get('/', [\App\Http\Controllers\User\NotificationController::class, 'index']);
                Route::get('/unread-count', [\App\Http\Controllers\User\NotificationController::class, 'unreadCount']);
                Route::post('/mark-read/{id}', [\App\Http\Controllers\User\NotificationController::class, 'markAsRead']);
                Route::post('/mark-all-read', [\App\Http\Controllers\User\NotificationController::class, 'markAllAsRead']);
            });

            // Admin Routes
            Route::prefix('admin')->group(function () {
                Route::get('/withdrawals', [\App\Http\Controllers\Admin\WithdrawalController::class, 'index']);
                Route::post('/withdrawals/{id}/approve', [\App\Http\Controllers\Admin\WithdrawalController::class, 'approve']);
                Route::post('/withdrawals/{id}/reject', [\App\Http\Controllers\Admin\WithdrawalController::class, 'reject']);
            });

            // Template Routes
            Route::get('/templates', [TemplateController::class, 'index']);
        });
    });
