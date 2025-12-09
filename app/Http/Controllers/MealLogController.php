<?php

namespace App\Http\Controllers;

use App\Models\MealLog;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MealLogController extends Controller
{
    
    public function index()
    {
        $user  = Auth::user();
        $today = Carbon::today();

       
        $mealLogs = MealLog::with(['recipe.carbonFootprint'])
            ->where('user_id', $user->id)
            ->orderByDesc('consumed_at')
            ->get();

       
        $todayTotals = [
            'calories' => 0,
            'protein'  => 0,
            'carbs'    => 0,
            'fiber'    => 0,
            'fat'      => 0,
            'co2'      => 0,
        ];

        foreach ($mealLogs as $log) {
            
            if (!$log->consumed_at || !$log->consumed_at->isSameDay($today)) {
                continue;
            }

            $recipe = $log->recipe;
            if (!$recipe) {
                continue;
            }

            $servings = $log->servings_consumed ?? 1;

            $todayTotals['calories'] += $servings * ($recipe->calories ?? 0);
            $todayTotals['protein']  += $servings * ($recipe->protein  ?? 0);
            $todayTotals['carbs']    += $servings * ($recipe->carbs    ?? 0);
            $todayTotals['fiber']    += $servings * ($recipe->fiber    ?? 0);
            $todayTotals['fat']      += $servings * ($recipe->fat      ?? 0);

            if ($recipe->carbonFootprint) {
                $todayTotals['co2'] += $servings * ($recipe->carbonFootprint->co2_emissions ?? 0);
            }
        }

        return view('meal_logs.index', [
            'mealLogs'    => $mealLogs,
            'today'       => $today,
            'todayTotals' => $todayTotals,
        ]);
    }

   
    public function create()
    {
        $recipes = Recipe::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        return view('meal_logs.create', [
            'recipes' => $recipes,
        ]);
    }

    
    public function store(Request $request)
    {
        $data = $request->validate([
            'recipe_id'         => ['required', 'exists:recipes,id'],
            'servings_consumed' => ['required', 'numeric', 'min:0.1'],
            'consumed_at'       => ['nullable', 'date'],
        ]);

        $data['user_id'] = Auth::id();

        if (empty($data['consumed_at'])) {
            $data['consumed_at'] = now();
        }

        MealLog::create($data);

        return redirect()
            ->route('meal_logs.index')
            ->with('success', 'Meal log added.');
    }

    
    public function edit(MealLog $meal_log)
    {
        $this->authorizeOwner($meal_log);

        $recipes = Recipe::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        return view('meal_logs.edit', [
            'mealLog' => $meal_log,
            'recipes' => $recipes,
        ]);
    }

    
    public function update(Request $request, MealLog $meal_log)
    {
        $this->authorizeOwner($meal_log);

        $data = $request->validate([
            'recipe_id'         => ['required', 'exists:recipes,id'],
            'servings_consumed' => ['required', 'numeric', 'min:0.1'],
            'consumed_at'       => ['nullable', 'date'],
        ]);

        if (empty($data['consumed_at'])) {
            $data['consumed_at'] = now();
        }

        $meal_log->update($data);

        return redirect()
            ->route('meal_logs.index')
            ->with('success', 'Meal log updated.');
    }

    
    public function destroy(MealLog $meal_log)
    {
        $this->authorizeOwner($meal_log);

        $meal_log->delete();

        return redirect()
            ->route('meal_logs.index')
            ->with('success', 'Meal log deleted.');
    }

    
    protected function authorizeOwner(MealLog $log): void
    {
        if ($log->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
