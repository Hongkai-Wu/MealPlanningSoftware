<?php

namespace App\Http\Controllers;

use App\Models\CalendarEntry;
use App\Models\Recipe;
use App\Services\GoalEvaluator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MealLogController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $entries = CalendarEntry::with(['recipe.nutrient'])
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        $todayEntries = CalendarEntry::with(['recipe.nutrient'])
            ->where('user_id', $user->id)
            ->whereDate('date', today())
            ->get();

        $recipes = Recipe::where('user_id', $user->id)->orderBy('name')->get();

        $todayNutrition = [
            'calories' => 0, 'protein' => 0, 'carbs' => 0,
            'fat' => 0, 'fiber' => 0, 'sugar' => 0, 'sodium' => 0,
        ];

        foreach ($todayEntries as $entry) {
            $n = $entry->nutrition;
            $todayNutrition['calories'] += $n['calories'];
            $todayNutrition['protein']  += $n['protein'];
            $todayNutrition['carbs']    += $n['carbs'];
            $todayNutrition['fat']      += $n['fat'];
            $todayNutrition['fiber']    += $n['fiber'];
            $todayNutrition['sugar']    += $n['sugar'];
            $todayNutrition['sodium']   += $n['sodium'];
        }

        foreach ($todayNutrition as $k => $v) {
            $todayNutrition[$k] = round($v, 2);
        }

        $goalsResult = (new GoalEvaluator())->evaluate($todayNutrition, $user->id);

        return view('meal_logs.index', compact('entries', 'recipes', 'todayNutrition', 'goalsResult'));
    }

    public function create()
{
    $userId = Auth::id();

    // Get the system user's ID (who owns public recipes)
    $systemId = \DB::table('users')->where('email', 'system@demo.local')->value('id');

    // Load both user's own recipes and system public recipes
    $recipes = \App\Models\Recipe::query()
        ->when($systemId, fn($q) => $q->where('user_id', $userId)->orWhere('user_id', $systemId))
        ->when(!$systemId, fn($q) => $q->where('user_id', $userId))
        ->orderBy('name')
        ->get();

    return view('meal_logs.create', compact('recipes'));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipe_id' => ['required', 'exists:recipes,id'],
            'date'      => ['required', 'date'],
            'meal_type' => ['required', 'in:Breakfast,Lunch,Dinner,Snack'],
            'servings'  => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        CalendarEntry::create([
            'user_id'   => Auth::id(),
            'recipe_id' => $validated['recipe_id'],
            'date'      => $validated['date'],
            'meal_type' => $validated['meal_type'],
            'servings'  => $validated['servings'],
        ]);

        return redirect()->route('meal_logs.index')->with('success', 'Meal log saved successfully.');
    }

    public function destroy(CalendarEntry $meal_log)
    {
        if ($meal_log->user_id !== Auth::id()) {
            return redirect()->route('meal_logs.index')->with('error', 'Unauthorized action.');
        }

        $meal_log->delete();

        return redirect()->route('meal_logs.index')->with('success', 'Meal log deleted successfully.');
    }
}