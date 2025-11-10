<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipeController; // 确保导入控制器

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// 所有需要登录 (auth:sanctum) 才能访问的路由
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Recipe 资源路由
    // 这将创建 /api/recipes, /api/recipes/{id}, POST /api/recipes 等标准 RESTful 路由
    Route::resource('recipes', RecipeController::class)->except(['create', 'edit']);
});