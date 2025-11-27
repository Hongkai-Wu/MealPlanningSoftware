<?php

namespace App\Http\Controllers;

use App\Models\CalendarEntry;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarEntryController extends Controller
{
    // 显示某一天的 Meal Plan（默认今天）
    public function index(Request $request)
    {
        $user = Auth::user();

        $dateString = $request->query('date');
        $date = $dateString ? Carbon::parse($dateString)->startOfDay() : Carbon::today();

        // 当前用户这一天的计划餐
        $entries = CalendarEntry::with('recipe')
            ->where('user_id', $user->id)
            ->whereDate('date', $date)
            ->orderBy('meal_type')
            ->get();

        // 当前用户可用的食谱
        $recipes = Recipe::where('user_id', $user->id)
            ->orderBy('name')
            ->get();

        // 计算当天“计划摄入”的营养总和（给 Dashboard / 本页使用）
        $plannedTotals = [
            'calories' => 0,
            'protein'  => 0,
            'carbs'    => 0,
            'fat'      => 0,
        ];

        foreach ($entries as $entry) {
            if ($entry->recipe) {
                $servings = $entry->servings ?? 1;
                $plannedTotals['calories'] += $servings * ($entry->recipe->calories ?? 0);
                $plannedTotals['protein']  += $servings * ($entry->recipe->protein ?? 0);
                $plannedTotals['carbs']    += $servings * ($entry->recipe->carbs ?? 0);
                $plannedTotals['fat']      += $servings * ($entry->recipe->fat ?? 0);
            }
        }

        return view('meal_plan.index', [
            'selectedDate'  => $date->toDateString(),
            'entries'       => $entries,
            'recipes'       => $recipes,
            'plannedTotals' => $plannedTotals,
        ]);
    }

    // 新增一条计划餐（从表单提交）
    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'date'      => ['required', 'date'],
            'meal_type' => ['required', 'in:breakfast,lunch,dinner,snack'],
            'recipe_id' => ['required', 'exists:recipes,id'],
            'servings'  => ['required', 'integer', 'min:1'],
        ]);

        $data['user_id'] = $user->id;

        CalendarEntry::create($data);

        return redirect()
            ->route('meal_plan.index', ['date' => $data['date']])
            ->with('success', 'Meal added to your plan.');
    }

    // 删除一条计划餐
    public function destroy(CalendarEntry $calendarEntry)
    {
        if ($calendarEntry->user_id !== Auth::id()) {
            abort(403);
        }

        $date = $calendarEntry->date;

        $calendarEntry->delete();

        return redirect()
            ->route('meal_plan.index', ['date' => $date])
            ->with('success', 'Meal removed from your plan.');
    }
}