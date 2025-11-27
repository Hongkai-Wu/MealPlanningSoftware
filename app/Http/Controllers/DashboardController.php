<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\MealLog;
use App\Models\CalendarEntry;
use App\Models\UserGoal;
use App\Models\Biometric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard.
     */
    public function index()
    {
        $user  = Auth::user();
        $today = Carbon::today();

        // 1. 今日实际摄入（来自 Meal Logs）
        $todayMealLogs = MealLog::with('recipe.carbonFootprint')
            ->where('user_id', $user->id)
            ->whereDate('consumed_at', $today)
            ->orderBy('consumed_at')
            ->get();

        $totals = [
            'calories' => 0,
            'protein'  => 0,
            'carbs'    => 0,
            'fat'      => 0,
            'co2'      => 0, // 实际碳足迹
        ];

        foreach ($todayMealLogs as $log) {
            $recipe = $log->recipe;
            if (! $recipe) {
                continue;
            }

            $servings = $log->servings_consumed ?? 1;

            $totals['calories'] += $servings * ($recipe->calories ?? 0);
            $totals['protein']  += $servings * ($recipe->protein  ?? 0);
            $totals['carbs']    += $servings * ($recipe->carbs    ?? 0);
            $totals['fat']      += $servings * ($recipe->fat      ?? 0);

            // 碳足迹 = 每份 CO2 × 份数（单位：kg CO2e）
            if ($recipe->carbonFootprint) {
                $co2PerServing = $recipe->carbonFootprint->co2_emissions ?? 0;
                $totals['co2'] += $servings * $co2PerServing;
            }
        }

        // 2. 今日计划摄入（来自 Calendar Entries）
        $todayCalendarEntries = CalendarEntry::with('recipe.carbonFootprint')
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->orderBy('meal_type')
            ->get();

        // 注意：这里一次性初始化，不要在 foreach 里面重复赋值
        $plannedTotals = [
            'calories' => 0,
            'protein'  => 0,
            'carbs'    => 0,
            'fat'      => 0,
            'co2'      => 0,
        ];

        foreach ($todayCalendarEntries as $entry) {
            $recipe = $entry->recipe;
            if (! $recipe) {
                continue;
            }

            $servings = $entry->servings ?? 1;

            $plannedTotals['calories'] += $servings * ($recipe->calories ?? 0);
            $plannedTotals['protein']  += $servings * ($recipe->protein  ?? 0);
            $plannedTotals['carbs']    += $servings * ($recipe->carbs    ?? 0);
            $plannedTotals['fat']      += $servings * ($recipe->fat      ?? 0);

            if ($recipe->carbonFootprint) {
                $co2PerServing = $recipe->carbonFootprint->co2_emissions ?? 0;
                $plannedTotals['co2'] += $servings * $co2PerServing;
            }
        }

        // 3. 当前激活的目标
        $activeGoals = UserGoal::where('user_id', $user->id)
            ->where('is_active', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $today);
            })
            ->get();

        // goal_type 和 Recipe 表字段的映射
        $goalColumnMap = [
            'calories' => 'calories',
            'protein'  => 'protein',
            'carbs'    => 'carbs',
            'fat'      => 'fat',
        ];

        // 4. 根据今日 totals 计算每个目标的达成状态
        $goalStatuses = [];
        foreach ($activeGoals as $goal) {
            $type = $goal->goal_type; // 例如 "protein"
            if (!array_key_exists($type, $goalColumnMap)) {
                // 暂时不支持的类型（比如 fiber、co2）先跳过
                continue;
            }

            $current = $totals[$type] ?? 0;
            $status  = 'on_track';

            if ($goal->direction === 'up') {
                // 想提高 —— 低于目标则 below
                if ($current + 1e-6 < $goal->target_value) {
                    $status = 'below';
                }
            } else {
                // 想降低 —— 超过目标则 over
                if ($current - 1e-6 > $goal->target_value) {
                    $status = 'over';
                }
            }

            $goalStatuses[$goal->id] = [
                'current' => $current,
                'status'  => $status,
            ];
        }

        // 5. 推荐食谱逻辑（基于未达成目标）
        $recommendedRecipeIds = [];

        foreach ($activeGoals as $goal) {
            $type = $goal->goal_type;

            if (!isset($goalColumnMap[$type])) {
                continue;
            }

            $statusData = $goalStatuses[$goal->id] ?? null;
            if (!$statusData) {
                continue;
            }

            $status = $statusData['status'];
            $column = $goalColumnMap[$type];

            // 情况 1：想“提高”的目标，但今天摄入“below” —— 推荐高这个营养的菜
            if ($goal->direction === 'up' && $status === 'below') {
                $ids = Recipe::where('user_id', $user->id)
                    ->orderByDesc($column)       // 营养多的排前面
                    ->limit(3)
                    ->pluck('id')
                    ->all();
                $recommendedRecipeIds = array_merge($recommendedRecipeIds, $ids);
            }

            // 情况 2：想“降低”的目标，但今天摄入“over” —— 推荐低这个营养的菜
            if ($goal->direction === 'down' && $status === 'over') {
                $ids = Recipe::where('user_id', $user->id)
                    ->orderBy($column)           // 营养少的排前面
                    ->limit(3)
                    ->pluck('id')
                    ->all();
                $recommendedRecipeIds = array_merge($recommendedRecipeIds, $ids);
            }
        }

        $recommendedRecipeIds = array_values(array_unique($recommendedRecipeIds));

        $recommendedRecipes = Recipe::whereIn('id', $recommendedRecipeIds)
            ->take(6)
            ->get();

        // 6. 一些统计数字
        $totalRecipes         = Recipe::where('user_id', $user->id)->count();
        $scheduledMealsToday  = $todayCalendarEntries->count();
        $biometricEntries     = Biometric::where('user_id', $user->id)->count();

        $latestBiometric = Biometric::where('user_id', $user->id)
            ->orderByDesc('measurement_date')
            ->orderByDesc('created_at')
            ->first();

        $statusMessage = null;
        if ($activeGoals->isEmpty()) {
            $statusMessage = 'You have no active goals yet. Set your nutrition goals to get personalised suggestions.';
        }

        return view('dashboard.index', [
            'title'                => 'Dashboard',
            'statusMessage'        => $statusMessage,
            'totals'               => $totals,
            'plannedTotals'        => $plannedTotals,
            'todayMealLogs'        => $todayMealLogs,
            'todayCalendarEntries' => $todayCalendarEntries,
            'totalRecipes'         => $totalRecipes,
            'scheduledMealsToday'  => $scheduledMealsToday,
            'activeGoals'          => $activeGoals,
            'goalStatuses'         => $goalStatuses,
            'biometricEntries'     => $biometricEntries,
            'latestBiometric'      => $latestBiometric,
            'recommendedRecipes'   => $recommendedRecipes,
        ]);
    }
}