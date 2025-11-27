<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create goal | Food Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8">

{{-- 统一导航栏，高亮 Goals --}}
@include('partials.navbar', ['active' => 'goals'])

<main class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4 border-b pb-3">
            <h1 class="text-2xl font-bold text-gray-900">
                Create a new goal
            </h1>
            <a href="{{ route('goals.index') }}"
               class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                Back to goals
            </a>
        </div>

        {{-- 错误提示 --}}
        @if($errors->any())
            <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 px-3 py-2 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('goals.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Goal type --}}
            <div>
                <label for="goal_type" class="block text-sm font-medium text-gray-700 mb-1">
                    Goal type <span class="text-red-500">*</span>
                </label>
                <select id="goal_type" name="goal_type"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500 @error('goal_type') border-red-500 @enderror"
                        required>
                    <option value="">Select goal type…</option>
                    <option value="calories" {{ old('goal_type')==='calories' ? 'selected' : '' }}>Calories</option>
                    <option value="protein"  {{ old('goal_type')==='protein'  ? 'selected' : '' }}>Protein</option>
                    <option value="carbs"    {{ old('goal_type')==='carbs'    ? 'selected' : '' }}>Carbohydrates</option>
                    <option value="fat"      {{ old('goal_type')==='fat'      ? 'selected' : '' }}>Fat</option>
                    <option value="fiber"    {{ old('goal_type')==='fiber'    ? 'selected' : '' }}>Fiber</option>
                    <option value="co2"      {{ old('goal_type')==='co2'      ? 'selected' : '' }}>Carbon footprint</option>
                </select>
                @error('goal_type')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Direction --}}
            <div>
                <label for="direction" class="block text-sm font-medium text-gray-700 mb-1">
                    Direction <span class="text-red-500">*</span>
                </label>
                <select id="direction" name="direction"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500 @error('direction') border-red-500 @enderror"
                        required>
                    <option value="up"   {{ old('direction')==='up'   ? 'selected' : '' }}>Increase (≥ target)</option>
                    <option value="down" {{ old('direction')==='down' ? 'selected' : '' }}>Decrease (≤ target)</option>
                </select>
                @error('direction')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Target value + unit --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label for="target_value" class="block text-sm font-medium text-gray-700 mb-1">
                        Target value <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.01" min="0"
                           id="target_value" name="target_value"
                           value="{{ old('target_value') }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500 @error('target_value') border-red-500 @enderror"
                           placeholder="e.g. 1800" required>
                    @error('target_value')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-1">
                        Unit <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="unit" name="unit"
                           value="{{ old('unit', 'kcal') }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500 @error('unit') border-red-500 @enderror"
                           placeholder="kcal, g, kg CO₂e" required>
                    @error('unit')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Dates --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Start date
                    </label>
                    <input type="date" id="start_date" name="start_date"
                           value="{{ old('start_date', now()->format('Y-m-d')) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500 @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                        End date (optional)
                    </label>
                    <input type="date" id="end_date" name="end_date"
                           value="{{ old('end_date') }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500 @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Active --}}
            <div class="flex items-center">
                <input id="is_active" name="is_active" type="checkbox" value="1"
                       class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                       {{ old('is_active', '1') ? 'checked' : '' }}>
                <label for="is_active" class="ml-2 block text-sm text-gray-700">
                    Active
                </label>
            </div>

            <div class="pt-3">
                <button type="submit"
                        class="w-full inline-flex justify-center px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-700">
                    Save goal
                </button>
            </div>
        </form>
    </div>
</main>

</body>
</html>
