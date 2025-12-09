<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Logs | Food Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8">

@include('partials.navbar', ['active' => 'meal_logs'])

<main class="max-w-5xl mx-auto">
    <div class="bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Meal Logs</h1>

        @if(session('success'))
            <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 px-3 py-2 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- 右上角新增按钮 --}}
        <div class="mb-4 flex justify-end">
            <a href="{{ route('meal_logs.create') }}"
               class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-700">
                Add meal log
            </a>
        </div>

        @if($mealLogs->isEmpty())
            <p class="text-sm text-gray-500">No meal logs yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="text-left py-2 px-2">Time</th>
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
                    @foreach($mealLogs as $log)
                        @php
                            $recipe   = $log->recipe;
                            $servings = $log->servings_consumed ?? 1;

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
                        @endphp
                        <tr class="border-b">
                            <td class="py-2 px-2">
                                {{ $log->consumed_at ? $log->consumed_at->format('Y-m-d H:i') : '-' }}
                            </td>
                            <td class="py-2 px-2">{{ $recipe->name ?? 'N/A' }}</td>
                            <td class="py-2 px-2">{{ $servings }}</td>
                            <td class="py-2 px-2">{{ round($calories) }} kcal</td>
                            <td class="py-2 px-2">{{ round($protein, 1) }} g</td>
                            <td class="py-2 px-2">{{ round($carbs, 1) }} g</td>
                            <td class="py-2 px-2">{{ round($fiber, 1) }} g</td>
                            <td class="py-2 px-2">{{ round($fat, 1) }} g</td>
                            <td class="py-2 px-2">{{ round($co2, 2) }} kg CO₂e</td>
                            <td class="py-2 px-2">
                                <a href="{{ route('meal_logs.edit', $log) }}"
                                   class="text-xs text-blue-600 hover:underline mr-2">Edit</a>
                                <form action="{{ route('meal_logs.destroy', $log) }}"
                                      method="POST"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs text-red-600 hover:underline"
                                            onclick="return confirm('Delete this meal log?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                    <tfoot>
                    <tr class="bg-gray-50 font-semibold border-t">
                        <td class="py-2 px-2" colspan="3">
                            Today totals ({{ $today->format('Y-m-d') }})
                        </td>
                        <td class="py-2 px-2">{{ round($todayTotals['calories']) }} kcal</td>
                        <td class="py-2 px-2">{{ round($todayTotals['protein'], 1) }} g</td>
                        <td class="py-2 px-2">{{ round($todayTotals['carbs'], 1) }} g</td>
                        <td class="py-2 px-2">{{ round($todayTotals['fiber'], 1) }} g</td>
                        <td class="py-2 px-2">{{ round($todayTotals['fat'], 1) }} g</td>
                        <td class="py-2 px-2">{{ round($todayTotals['co2'], 2) }} kg CO₂e</td>
                        <td></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</main>

</body>
</html>
