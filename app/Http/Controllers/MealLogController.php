<?php

namespace App\Http\Controllers;

use App\Models\MealLog;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MealLogController extends Controller
{
    // 列表：当前用户所有 Meal Logs（按时间倒序）
    public function index()
    {
        $user = Auth::user();

        $mealLogs = MealLog::with('recipe')
            ->where('user_id', $user->id)
            ->orderByDesc('consumed_at')
            ->paginate(10);

        return view('meal_logs.index', [
            'mealLogs' => $mealLogs,
        ]);
    }

    // 显示创建表单
    public function create()
    {
        $user = Auth::user();

        $recipes = Recipe::where('user_id', $user->id)
            ->orderBy('name')
            ->get();

        return view('meal_logs.create', [
            'recipes' => $recipes,
            'defaultConsumedAt' => now()->format('Y-m-d\TH:i'),
        ]);
    }

    // 保存新记录
    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'recipe_id' => ['required', 'exists:recipes,id'],
            'servings_consumed' => ['required', 'numeric', 'min:0.1'],
            'consumed_at' => ['nullable', 'date'],
        ]);

        $data['user_id'] = $user->id;
        if (empty($data['consumed_at'])) {
            $data['consumed_at'] = now();
        }

        MealLog::create($data);

        return redirect()
            ->route('meal_logs.index')
            ->with('success', 'Meal log added.');
    }

    // 删除一条记录（可选）
    public function destroy(MealLog $meal_log)
    {
        if ($meal_log->user_id !== Auth::id()) {
            abort(403);
        }

        $meal_log->delete();

        return redirect()
            ->route('meal_logs.index')
            ->with('success', 'Meal log deleted.');
    }
}
