<?php

namespace App\Http\Controllers;

use App\Models\Biometric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BiometricController extends Controller
{
    // 列表：当前用户所有健康记录
    public function index()
    {
        $biometrics = Biometric::where('user_id', Auth::id())
            ->orderByDesc('measurement_date')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('biometrics.index', [
            'biometrics' => $biometrics,
        ]);
    }

    // 显示创建表单
    public function create()
    {
        return view('biometrics.create', [
            'defaultDate' => now()->toDateString(),
        ]);
    }

    // 保存新纪录
    public function store(Request $request)
    {
        $data = $request->validate([
            'measurement_date' => ['required', 'date'],
            'weight'          => ['nullable', 'numeric', 'min:0'],
            'systolic_bp'     => ['nullable', 'integer', 'min:0'],
            'diastolic_bp'    => ['nullable', 'integer', 'min:0'],
            'bmi'             => ['nullable', 'numeric', 'min:0'],
            'notes'           => ['nullable', 'string'],
        ]);

        $data['user_id'] = Auth::id();

        Biometric::create($data);

        return redirect()
            ->route('biometrics.index')
            ->with('success', 'Measurement added.');
    }

    // 编辑表单
    public function edit(Biometric $biometric)
    {
        if ($biometric->user_id !== Auth::id()) {
            abort(403);
        }

        return view('biometrics.edit', [
            'biometric' => $biometric,
        ]);
    }

    // 更新
    public function update(Request $request, Biometric $biometric)
    {
        if ($biometric->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'measurement_date' => ['required', 'date'],
            'weight'          => ['nullable', 'numeric', 'min:0'],
            'systolic_bp'     => ['nullable', 'integer', 'min:0'],
            'diastolic_bp'    => ['nullable', 'integer', 'min:0'],
            'bmi'             => ['nullable', 'numeric', 'min:0'],
            'notes'           => ['nullable', 'string'],
        ]);

        $biometric->update($data);

        return redirect()
            ->route('biometrics.index')
            ->with('success', 'Measurement updated.');
    }

    // 删除
    public function destroy(Biometric $biometric)
    {
        if ($biometric->user_id !== Auth::id()) {
            abort(403);
        }

        $biometric->delete();

        return redirect()
            ->route('biometrics.index')
            ->with('success', 'Measurement deleted.');
    }
}