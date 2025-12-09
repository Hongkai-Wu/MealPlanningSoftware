<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MealLogController;
use App\Http\Controllers\CalendarEntryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\BiometricController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {

    // 仪表盘
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // 用户资料
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
  
     // Meal Plan 页面（查看 + 添加计划）
    Route::get('/meal-plan', [CalendarEntryController::class, 'index'])
        ->name('meal_plan.index');

   
    Route::post('/meal-records', [CalendarEntryController::class, 'store'])
        ->name('calendar_entries.store');
    Route::delete('/meal-records/{calendarEntry}', [CalendarEntryController::class, 'destroy'])
        ->name('calendar_entries.destroy');

    // Meal Logs（实际吃的东西）
    Route::resource('meal_logs', MealLogController::class);

    // 食谱管理
    Route::resource('recipes', RecipeController::class)->except(['show']);

    // 目标管理
    Route::resource('goals', GoalController::class)->except(['show']);
});

    // Biometrics（体重、血压等）
    Route::resource('biometrics', BiometricController::class)->except(['show']);

require __DIR__ . '/auth.php';
