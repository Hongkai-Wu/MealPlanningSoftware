<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add new recipe | Food Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8">

@include('partials.navbar', ['active' => 'recipes'])

<main class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Add new recipe</h1>

        <form action="{{ route('recipes.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border rounded px-3 py-2 text-sm" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Serving size</label>
                <input type="text" name="serving_size" value="{{ old('serving_size') }}"
                       placeholder="e.g. 1 bowl, 100 g cooked rice"
                       class="w-full border rounded px-3 py-2 text-sm" required>
            </div>

           <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Calories (kcal)</label>
        <input type="number" name="calories" step="0.01" min="0"
               class="mt-1 block w-full border-gray-300 rounded-md"
               value="{{ old('calories') }}">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Protein (g)</label>
        <input type="number" name="protein" step="0.01" min="0"
               class="mt-1 block w-full border-gray-300 rounded-md"
               value="{{ old('protein') }}">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Carbs (g)</label>
        <input type="number" name="carbs" step="0.01" min="0"
               class="mt-1 block w-full border-gray-300 rounded-md"
               value="{{ old('carbs') }}">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Fat (g)</label>
        <input type="number" name="fat" step="0.01" min="0"
               class="mt-1 block w-full border-gray-300 rounded-md"
               value="{{ old('fat') }}">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Fiber (g)</label>
        <input type="number" name="fiber" step="0.01" min="0"
               class="mt-1 block w-full border-gray-300 rounded-md"
               value="{{ old('fiber') }}">
    </div>
</div>

            
            <div class="mt-4 border-t pt-4">
                <h2 class="text-sm font-semibold text-gray-700 mb-2">Carbon footprint (optional)</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            CO₂ per serving (kg CO₂e)
                        </label>
                        <input type="number" step="0.01" min="0" name="co2_emissions"
                               value="{{ old('co2_emissions') }}"
                               class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Notes (optional)
                        </label>
                        <input type="text" name="co2_notes"
                               value="{{ old('co2_notes') }}"
                               placeholder="e.g. source of calculation"
                               class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Description (optional)
                </label>
                <textarea name="description" rows="3"
                          class="w-full border rounded px-3 py-2 text-sm">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center space-x-4 mt-4">
                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-700">
                    Save recipe
                </button>
                <a href="{{ route('recipes.index') }}"
                   class="text-sm text-gray-600 hover:underline">
                    Cancel
                </a >
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