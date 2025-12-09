<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Plan | Food Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8">

@include('partials.navbar', ['active' => 'meal_plan'])

<main class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Meal Plan (Today)</h1>

        @if(session('success'))
            <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 px-3 py-2 rounded">
                {{ session('success') }}
            </div>
        @endif

        @php
            $entries = $entries ?? collect();

            $totals = [
                'calories' => 0,
                'protein'  => 0,
                'carbs'    => 0,
                'fiber'    => 0,
                'fat'      => 0,
                'co2'      => 0,
            ];
        @endphp

        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Scheduled meals</h2>

            @if($entries->isEmpty())
                <p class="text-sm text-gray-500">No meals scheduled for today.</p >
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="text-left py-2 px-2">Meal</th>
                            <th class="text-left py-2 px-2">Recipe</th>
                            <th class="text-left py-2 px-2">Servings</th>
                            <th class="text-left py-2 px-2">Calories</th>
                            <th class="text-left py-2 px-2">Protein</th>
                            <th class="text-left py-2 px-2">Carbs</th>
                            <th class="text-left py-2 px-2">Fiber</th>
                            <th class="text-left py-2 px-2">Fat</th>
                            <th class="text-left py-2 px-2">CO₂</th>
                            <th class="text-left py-2 px-2">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($entries as $entry)
                            @php
                                $recipe   = $entry->recipe;
                                $servings = $entry->servings ?? 1;

                                $calories = $recipe ? $servings * ($recipe->calories ?? 0) : 0;
                                $protein  = $recipe ? $servings * ($recipe->protein  ?? 0) : 0;
                                $carbs    = $recipe ? $servings * ($recipe->carbs    ?? 0) : 0;
                                $fiber    = $recipe ? $servings * ($recipe->fiber    ?? 0) : 0;
                                $fat      = $recipe ? $servings * ($recipe->fat      ?? 0) : 0;

                                $co2 = 0;
                                if ($recipe && $recipe->carbonFootprint) {
                                    $co2PerServing = $recipe->carbonFootprint->co2_emissions ?? 0;
                                    $co2 = $servings * $co2PerServing;
                                }

                                $totals['calories'] += $calories;
                                $totals['protein']  += $protein;
                                $totals['carbs']    += $carbs;
                                $totals['fiber']    += $fiber;
                                $totals['fat']      += $fat;
                                $totals['co2']      += $co2;
                            @endphp

                            <tr class="border-b">
                                <td class="py-2 px-2 capitalize">{{ $entry->meal_type }}</td>
                                <td class="py-2 px-2">{{ $recipe->name ?? 'N/A' }}</td>
                                <td class="py-2 px-2">{{ $servings }}</td>
                                <td class="py-2 px-2">{{ round($calories) }} kcal</td>
                                <td class="py-2 px-2">{{ round($protein, 1) }} g</td>
                                <td class="py-2 px-2">{{ round($carbs, 1) }} g</td>
                                <td class="py-2 px-2">{{ round($fiber, 1) }} g</td>
                                <td class="py-2 px-2">{{ round($fat, 1) }} g</td>
                                <td class="py-2 px-2">{{ round($co2, 2) }} kg CO₂e</td>
                                <td class="py-2 px-2">
                                    <form action="{{ route('calendar_entries.destroy', $entry) }}"
                                          method="POST"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-xs text-red-600 hover:underline"
                                                onclick="return confirm('Remove this meal from plan?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr class="bg-gray-50 font-semibold border-t">
                            <td class="py-2 px-2" colspan="3">Today planned totals</td>
                            <td class="py-2 px-2">{{ round($totals['calories']) }} kcal</td>
                            <td class="py-2 px-2">{{ round($totals['protein'], 1) }} g</td>
                            <td class="py-2 px-2">{{ round($totals['carbs'], 1) }} g</td>
                            <td class="py-2 px-2">{{ round($totals['fiber'], 1) }} g</td>
                            <td class="py-2 px-2">{{ round($totals['fat'], 1) }} g</td>
                            <td class="py-2 px-2">{{ round($totals['co2'], 2) }} kg CO₂e</td>
                            <td></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>

        <div class="border-t pt-4 mt-4">
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Add meal to today</h2>
            <form action="{{ route('calendar_entries.store') }}" method="POST"
                  class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf

                {{-- 传今天的日期，避免 “The date field is required” --}}
                <input type="hidden" name="date" value="{{ now()->toDateString() }}">

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Meal type</label>
                    <select name="meal_type" class="w-full border rounded px-3 py-2 text-sm" required>
                        <option value="breakfast">Breakfast</option>
                        <option value="lunch">Lunch</option>
                        <option value="dinner">Dinner</option>
                        <option value="snack">Snack</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Recipe</label>
                    <select name="recipe_id" class="w-full border rounded px-3 py-2 text-sm" required>
                        @foreach($recipes as $recipe)
                            <option value="{{ $recipe->id }}">{{ $recipe->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Servings</label>
                    <input type="number" name="servings" min="1" step="1" value="1"
                           class="w-full border rounded px-3 py-2 text-sm" required>
                </div>

                <div>
                    <button type="submit"
                            class="w-full px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-700">
                        Add
                    </button>
                </div>
            </form>

            @if($errors->any())
                <div class="mt-3 text-sm text-red-600">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</main>

</body>
</html>