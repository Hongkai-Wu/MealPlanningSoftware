<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log a Meal | Food Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8">
@include('partials.navbar', ['active' => 'meal_logs'])

<main class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Log a meal</h1>

        <form action="{{ route('meal_logs.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Recipe
                </label>
                <select name="recipe_id"
                        class="w-full border rounded px-2 py-1 text-sm"
                        required>
                    <option value="">-- Select recipe --</option>
                    @foreach($recipes as $recipe)
                        <option value="{{ $recipe->id }}"
                            {{ old('recipe_id') == $recipe->id ? 'selected' : '' }}>
                            {{ $recipe->name }} ({{ $recipe->calories }} kcal / {{ $recipe->serving_size }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Servings consumed
                    </label>
                    <input type="number" name="servings_consumed" step="0.1" min="0.1"
                           value="{{ old('servings_consumed', 1) }}"
                           class="w-full border rounded px-2 py-1 text-sm"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Time (optional)
                    </label>
                    <input type="datetime-local" name="consumed_at"
                           value="{{ old('consumed_at', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full border rounded px-2 py-1 text-sm">
                </div>
            </div>

            <div class="flex items-center space-x-3 mt-4">
                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-700">
                    Save
                </button>
                <a href="{{ route('meal_logs.index') }}"
                   class="text-sm text-gray-600 hover:underline">
                    Cancel
                </a>
            </div>

            @if($errors->any())
                <div class="mt-3 text-sm text-red-600">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </form>
    </div>
</main>

</body>
</html>
