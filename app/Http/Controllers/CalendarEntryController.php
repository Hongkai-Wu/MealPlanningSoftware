<?php

namespace App\Http\Controllers;

use App\Services\GoalEvaluator;
use App\Models\CalendarEntry;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CalendarEntryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $entries = CalendarEntry::with('recipe')
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();
        
        $recipes = Recipe::all();
        $todayNutrition = $this->getTodayNutrition($user->id);

        $evaluator = new GoalEvaluator();
$goalsResult = $evaluator->evaluate($todayNutrition, $user->id);

        return view('calendar_entries.index', compact('entries', 'recipes', 'todayNutrition', 'goalsResult'));

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'date' => 'required|date',
            'meal_type' => 'required|in:Breakfast,Lunch,Dinner,Snack',
            'servings' => 'required|integer|min:1|max:10',
        ]);

        $entry = CalendarEntry::create([
            'user_id' => Auth::id(),
            'recipe_id' => $validated['recipe_id'],
            'date' => $validated['date'],
            'meal_type' => $validated['meal_type'],
            'servings' => $validated['servings'],
        ]);

        Log::info('Calendar entry created', ['user_id' => Auth::id(), 'entry_id' => $entry->id]);

        return redirect()->route('calendar_entries.index')
            ->with('success', '膳食记录已添加！数据成功写入 calendar_entries 表。');
    }

    public function destroy(CalendarEntry $calendarEntry)
    {
        $this->authorize('delete', $calendarEntry);
        $calendarEntry->delete();
        
        return redirect()->route('calendar_entries.index')
            ->with('success', 'record deleted');
    }

    private function getTodayNutrition($userId)
    {
        $todayEntries = CalendarEntry::with('recipe')
            ->where('user_id', $userId)
            ->whereDate('date', today())
            ->get();

        $totals = ['protein' => 0, 'carbs' => 0, 'fiber' => 0, 'calories' => 0];
        
        foreach ($todayEntries as $entry) {
            $nutrition = $entry->nutrition;
            $totals['protein'] += $nutrition['protein'];
            $totals['carbs'] += $nutrition['carbs'];
            $totals['fiber'] += $nutrition['fiber'];
            $totals['calories'] += $nutrition['calories'];
        }

        return $totals;
    }
}
