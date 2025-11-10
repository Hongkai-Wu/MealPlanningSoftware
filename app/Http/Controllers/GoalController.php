<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Nutrient; // 假设您有一个 Nutrient 模型
use App\Http\Requests\GoalRequest; // 引入我们创建的验证请求
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    /**
     * 显示当前用户的所有目标列表。
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // 仅获取当前认证用户设定的目标，并按ID降序排列（最新创建的在前面）
        $goals = Goal::with('nutrient')
                    ->where('user_id', Auth::id())
                    ->latest()
                    ->get();

        // 假设视图文件名为 goals.index
        return view('goals.index', compact('goals'));
    }

    /**
     * 显示创建新目标的表单。
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // 获取所有可供选择的营养素，用于表单中的下拉菜单
        $nutrients = Nutrient::all(['id', 'name', 'unit']);

        // 假设视图文件名为 goals.create_or_edit
        return view('goals.create_or_edit', [
            'nutrients' => $nutrients,
            'goal' => new Goal(), // 传递一个空的 Goal 实例用于创建
            'mode' => 'create' // 明确告诉视图当前是创建模式
        ]);
    }

    /**
     * 存储新创建的目标。
     *
     * @param  \App\Http\Requests\GoalRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(GoalRequest $request)
    {
        // GoalRequest 已经自动处理了所有输入验证

        $data = $request->validated();
        
        // 自动将当前用户的 ID 注入到数据中，确保目标属于当前用户
        $data['user_id'] = Auth::id();

        Goal::create($data);

        return redirect()
            ->route('goals.index') // 重定向到目标列表页
            ->with('success', '目标创建成功！'); // 附加成功的消息
    }

    /**
     * 显示编辑指定目标的表单。
     *
     * @param  \App\Models\Goal  $goal
     * @return \Illuminate\View\View
     */
    public function edit(Goal $goal)
    {
        // 策略：确保该目标属于当前用户，防止越权访问
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // 获取所有可供选择的营养素
        $nutrients = Nutrient::all(['id', 'name', 'unit']);

        // 假设视图文件名为 goals.create_or_edit
        return view('goals.create_or_edit', [
            'nutrients' => $nutrients,
            'goal' => $goal,
            'mode' => 'edit' // 明确告诉视图当前是编辑模式
        ]);
    }

    /**
     * 更新指定目标。
     *
     * @param  \App\Http\Requests\GoalRequest  $request
     * @param  \App\Models\Goal  $goal
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(GoalRequest $request, Goal $goal)
    {
        // 策略：再次检查目标所有权
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // GoalRequest 已经自动处理了所有输入验证
        $goal->update($request->validated());

        return redirect()
            ->route('goals.index')
            ->with('success', '目标更新成功！');
    }

    /**
     * 删除指定目标。
     *
     * @param  \App\Models\Goal  $goal
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Goal $goal)
    {
        // 策略：再次检查目标所有权
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $goal->delete();

        return redirect()
            ->route('goals.index')
            ->with('success', '目标删除成功！');
    }
}
