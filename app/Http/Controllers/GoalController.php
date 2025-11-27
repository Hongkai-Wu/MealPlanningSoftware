<?php

namespace App\Http\Controllers;

use App\Models\UserGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    // 列出当前用户的所有目标
    public function index()
    {
        $user = Auth::user();

        $goals = UserGoal::where('user_id', $user->id)
            ->orderBy('goal_type')
            ->get();

        // 为了在下拉框里用
        $goalTypes = [
            'calories' => 'Calories',
            'protein'  => 'Protein',
            'carbs'    => 'Carbohydrates',
            'fat'      => 'Fat',
            'fiber'    => 'Fiber',
            'co2'      => 'Carbon footprint',
        ];

        return view('goals.index', [
            'user'      => $user,
            'goals'     => $goals,
            'goalTypes' => $goalTypes,
        ]);
    }

    // 处理“新建目标”提交
    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'goal_type'    => 'required|string',
            'target_value' => 'required|numeric',
            'direction'    => 'required|in:up,down',
            'unit'         => 'required|string|max:20',
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'is_active'    => 'nullable',
        ]);

        $data['user_id']  = $user->id;
        $data['is_active'] = $request->boolean('is_active');

        UserGoal::create($data);

        return redirect()
            ->route('goals.index')
            ->with('success', 'Goal created successfully.');
    }

    // 显示编辑表单
    public function edit(UserGoal $goal)
    {
        $this->authorizeGoal($goal);

        $goalTypes = [
            'calories' => 'Calories',
            'protein'  => 'Protein',
            'carbs'    => 'Carbohydrates',
            'fat'      => 'Fat',
            'fiber'    => 'Fiber',
            'co2'      => 'Carbon footprint',
        ];

        return view('goals.edit', [
            'goal'      => $goal,
            'goalTypes' => $goalTypes,
        ]);
    }

    // 处理更新
    public function update(Request $request, UserGoal $goal)
    {
        $this->authorizeGoal($goal);

        $data = $request->validate([
            'goal_type'    => 'required|string',
            'target_value' => 'required|numeric',
            'direction'    => 'required|in:up,down',
            'unit'         => 'required|string|max:20',
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'is_active'    => 'nullable',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $goal->update($data);

        return redirect()
            ->route('goals.index')
            ->with('success', 'Goal updated successfully.');
    }

    // 删除目标
    public function destroy(UserGoal $goal)
    {
        $this->authorizeGoal($goal);

        $goal->delete();

        return redirect()
            ->route('goals.index')
            ->with('success', 'Goal deleted.');
    }

    // 确保用户只能操作自己的目标
    protected function authorizeGoal(UserGoal $goal): void
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}