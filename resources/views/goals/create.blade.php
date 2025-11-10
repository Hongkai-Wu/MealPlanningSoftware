@extends('layouts.app')

@section('title', '设定新的膳食目标')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-xl mx-auto">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
            <div class="flex items-center justify-between border-b pb-4 mb-4">
                <h1 class="text-2xl font-bold text-gray-900">设定新的膳食目标</h1>
                <a href="{{ route('goals.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    返回目标列表
                </a>
            </div>

            {{-- 表单开始 --}}
            <form action="{{ route('goals.store') }}" method="POST">
                @csrf

                {{-- 1. 营养素选择 (Nutrient Select) --}}
                <div class="mb-5">
                    <label for="nutrient_id" class="block text-sm font-medium text-gray-700 mb-1">选择营养素 <span class="text-red-500">*</span></label>
                    <select id="nutrient_id" name="nutrient_id" 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg @error('nutrient_id') border-red-500 @enderror">
                        <option value="">-- 请选择一个营养素 --</option>
                        @foreach ($nutrients as $nutrient)
                            {{-- 使用 old() 函数在验证失败时保留用户选择 --}}
                            <option value="{{ $nutrient->id }}" {{ old('nutrient_id') == $nutrient->id ? 'selected' : '' }}>
                                {{ $nutrient->name }} (单位: {{ $nutrient->unit }})
                            </option>
                        @endforeach
                    </select>
                    @error('nutrient_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 2. 目标值 (Target Value) --}}
                <div class="mb-5">
                    <label for="target_value" class="block text-sm font-medium text-gray-700 mb-1">目标值 <span class="text-red-500">*</span></label>
                    <div class="mt-1 relative rounded-lg shadow-sm">
                        <input type="number" name="target_value" id="target_value" value="{{ old('target_value') }}" min="0" step="0.01" 
                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-lg @error('target_value') border-red-500 @enderror" 
                               placeholder="例如: 1500" required>
                        {{-- 动态显示单位的 JavaScript (符合“至少一个地方使用 JS”的要求) --}}
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm" id="nutrient-unit">
                                --
                            </span>
                        </div>
                    </div>
                    @error('target_value')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 3. 目标类型 (Is Up Goal) --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">目标类型 <span class="text-red-500">*</span></label>
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center">
                            <input id="up_goal_true" name="is_up_goal" type="radio" value="1" 
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                                   {{ old('is_up_goal') === '1' || old('is_up_goal') === null ? 'checked' : '' }} required>
                            <label for="up_goal_true" class="ml-2 block text-sm text-gray-900">
                                达到或超过 (≥)
                                <p class="text-xs text-gray-500">（通常用于蛋白质、维生素等）</p>
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="up_goal_false" name="is_up_goal" type="radio" value="0" 
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                                   {{ old('is_up_goal') === '0' ? 'checked' : '' }} required>
                            <label for="up_goal_false" class="ml-2 block text-sm text-gray-900">
                                低于或等于 (≤)
                                <p class="text-xs text-gray-500">（通常用于糖分、饱和脂肪等）</p>
                            </label>
                        </div>
                    </div>
                    @error('is_up_goal')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 4. 开始日期 (Start Date) --}}
                <div class="mb-6">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">开始日期 (可选)</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', \Carbon\Carbon::now()->format('Y-m-d')) }}"
                           class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 提交按钮 --}}
                <div class="flex justify-end">
                    <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        确认设定目标
                    </button>
                </div>
            </form>
            {{-- 表单结束 --}}

        </div>
    </div>
</div>

<script>
    // 这是一个满足“至少一个地方使用 JS”要求的简单交互
    document.addEventListener('DOMContentLoaded', function() {
        const nutrientSelect = document.getElementById('nutrient_id');
        const unitDisplay = document.getElementById('nutrient-unit');
        
        // 营养素数据（从 Blade 中传入）
        // 我们将营养素列表转换为一个方便 JavaScript 查找的对象
        const nutrientsData = {
            @foreach ($nutrients as $nutrient)
                {{ $nutrient->id }}: '{{ $nutrient->unit }}',
            @endforeach
        };

        function updateUnitDisplay() {
            const selectedId = nutrientSelect.value;
            const unit = nutrientsData[selectedId] || '--';
            unitDisplay.textContent = unit;
        }

        // 监听选择框变化
        nutrientSelect.addEventListener('change', updateUnitDisplay);

        // 初始化时运行一次，确保页面加载时单位正确
        updateUnitDisplay();
    });
</script>
@endsection
