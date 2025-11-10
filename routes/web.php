
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarEntryController;
use App\Http\Controllers\MealLogController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Default route
Route::get('/', function () {
    return view('welcome');
});

// Authenticated Routes (Protected by 'auth' middleware)
// 使用资源路由 (Route::resource) 替换了之前手动定义的 MealLog 路由
Route::middleware(['auth'])->group(function () {
    
    // Dashboard Route
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // User Profile Routes 
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Calendar Entry Related Routes
    Route::post('/meal-records', [CalendarEntryController::class, 'store'])
        ->name('calendar_entries.store');

    Route::delete('/meal-records/{calendarEntry}', [CalendarEntryController::class, 'destroy'])
        ->name('calendar_entries.destroy');

    // Meal Logs Routes - 资源路由，包含了 CRUD 所需的所有 7 个动作 (index, create, store, show, edit, update, destroy)
    Route::resource('meal_logs', MealLogController::class);

});

require __DIR__.'/auth.php';