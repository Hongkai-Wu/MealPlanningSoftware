<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\MealLog;
use App\Models\CalendarEntry;
use App\Models\UserGoal;
use App\Models\Biometric;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    
    public function index()
    {
        $user  = Auth::user();
        $today = Carbon::today();

       
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
            'fiber'    => 0,  
            'co2'      => 0,
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
            $totals['fiber']    += $servings * ($recipe->fiber    ?? 0);  

           
            if ($recipe->carbonFootprint) {
                $co2PerServing = $recipe->carbonFootprint->co2_emissions ?? 0;
                $totals['co2'] += $servings * $co2PerServing;
            }
        }

        
        $todayCalendarEntries = CalendarEntry::with('recipe.carbonFootprint')
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->orderBy('meal_type')
            ->get();

        $plannedTotals = [
            'calories' => 0,
            'protein'  => 0,
            'carbs'    => 0,
            'fat'      => 0,
            'fiber'    => 0, 
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
            $plannedTotals['fiber']    += $servings * ($recipe->fiber    ?? 0); 

            if ($recipe->carbonFootprint) {
                $co2PerServing = $recipe->carbonFootprint->co2_emissions ?? 0;
                $plannedTotals['co2'] += $servings * $co2PerServing;
            }
        }

        
        $activeGoals = UserGoal::where('user_id', $user->id)
            ->where('is_active', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $today);
            })
            ->get();

      
        $goalColumnMap = [
            'calories' => 'calories',
            'protein'  => 'protein',
            'carbs'    => 'carbs',
            'fat'      => 'fat',
            'fiber'    => 'fiber',   
        ];

     
        $goalStatuses = [];

        foreach ($activeGoals as $goal) {
            $type = $goal->goal_type; // 例如 "protein" / "fiber"

            if (!array_key_exists($type, $goalColumnMap)) {
     
                continue;
            }

            $current = $totals[$type] ?? 0;
            $status  = 'on_track';

            if ($goal->direction === 'up') {
              
                if ($current + 1e-6 < $goal->target_value) {
                    $status = 'below';
                }
            } else {
            
                if ($current - 1e-6 > $goal->target_value) {
                    $status = 'over';
                }
            }

            $goalStatuses[$goal->id] = [
                'current' => $current,
                'status'  => $status,
            ];
        }

       
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

            
            if ($goal->direction === 'up' && $status === 'below') {
                $ids = Recipe::where('user_id', $user->id)
                    ->orderByDesc($column)       
                    ->limit(3)
                    ->pluck('id')
                    ->all();

                $recommendedRecipeIds = array_merge($recommendedRecipeIds, $ids);
            }

          
            if ($goal->direction === 'down' && $status === 'over') {
                $ids = Recipe::where('user_id', $user->id)
                    ->orderBy($column)           
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

        
        $totalRecipes        = Recipe::where('user_id', $user->id)->count();
        $scheduledMealsToday = $todayCalendarEntries->count();
        $biometricEntries    = Biometric::where('user_id', $user->id)->count();

        $latestBiometric = Biometric::where('user_id', $user->id)
            ->orderByDesc('measurement_date')
            ->orderByDesc('created_at')
            ->first();

     $previousBiometric = null;

if ($latestBiometric) {
    $previousBiometric = Biometric::where('user_id', $user->id)
        ->where(function ($q) use ($latestBiometric) {
            
            $q->where('measurement_date', '<', $latestBiometric->measurement_date)
              ->orWhere(function ($q2) use ($latestBiometric) {
                  $q2->where('measurement_date', $latestBiometric->measurement_date)
                     ->where('id', '<', $latestBiometric->id);
              });
        })
        ->orderByDesc('measurement_date')
        ->orderByDesc('created_at')
        ->first();
}


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
            'previousBiometric'   => $previousBiometric,
           'recommendedRecipes'   => $recommendedRecipes,
        ]);
    }
}
