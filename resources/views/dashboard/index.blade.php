<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} | Food Goals Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
        }
    </style>
</head>

<body class="min-h-screen p-4 md:p-8">

@include('partials.navbar', ['active' => 'dashboard'])

<main>
    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $title ?? 'Dashboard' }}</h1>
    <p class="text-gray-600 mb-6">{{ $statusMessage ?? '' }}</p>

    {{-- 今日营养汇总（实际摄入） --}}
    <section class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-xl shadow">
            <div class="text-xs text-gray-500 uppercase">Calories (logged)</div>
            <div class="text-2xl font-bold text-gray-800">
                {{ round($totals['calories']) }} <span class="text-sm font-normal">kcal</span>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <div class="text-xs text-gray-500 uppercase">Protein (logged)</div>
            <div class="text-2xl font-bold text-gray-800">
                {{ round($totals['protein'], 1) }} <span class="text-sm font-normal">g</span>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <div class="text-xs text-gray-500 uppercase">Carbs (logged)</div>
            <div class="text-2xl font-bold text-gray-800">
                {{ round($totals['carbs'], 1) }} <span class="text-sm font-normal">g</span>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <div class="text-xs text-gray-500 uppercase">Fat (logged)</div>
            <div class="text-2xl font-bold text-gray-800">
                {{ round($totals['fat'], 1) }} <span class="text-sm font-normal">g</span>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <div class="text-xs text-gray-500 uppercase">Carbon footprint</div>
            <div class="text-2xl font-bold text-gray-800">
                {{ round($totals['co2'] ?? 0, 2) }} <span class="text-sm font-normal">kg CO₂e</span>
            </div>
        </div>
    </section>

    {{-- Goal Status Overview：目标 + 今日进度 --}}
    <section class="mb-8 p-6 bg-white rounded-xl shadow-lg">
        <div class="flex items-center justify-between mb-4 border-b pb-2">
            <h2 class="text-xl font-semibold text-gray-700">Goal Status Overview</h2>
            <a href="{{ route('goals.index') }}" class="text-sm text-blue-600 hover:underline">
                Manage goals
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @if($activeGoals->isEmpty())
                <p class="text-gray-500 text-sm">You have no active goals yet.</p>
            @else
                @foreach($activeGoals as $goal)
                    @php
                        $statusData = $goalStatuses[$goal->id] ?? null;
                        $status = $statusData['status'] ?? 'on_track';
                        $current = $statusData['current'] ?? 0;

                        $bgClass     = 'bg-green-50';
                        $borderClass = 'border-green-200';
                        $titleColor  = 'text-green-600';
                        $valueColor  = 'text-green-800';
                        $statusColor = 'text-green-600';

                        if ($status === 'below') {
                            $bgClass     = 'bg-yellow-50';
                            $borderClass = 'border-yellow-200';
                            $titleColor  = 'text-yellow-600';
                            $valueColor  = 'text-yellow-800';
                            $statusColor = 'text-yellow-600';
                        } elseif ($status === 'over') {
                            $bgClass     = 'bg-red-50';
                            $borderClass = 'border-red-200';
                            $titleColor  = 'text-red-600';
                            $valueColor  = 'text-red-800';
                            $statusColor = 'text-red-600';
                        }
                    @endphp

                    <div class="{{ $bgClass }} p-4 rounded-lg border {{ $borderClass }}">
                        <div class="text-sm font-medium {{ $titleColor }}">
                            {{ ucfirst($goal->goal_type) }} goal
                        </div>

                        <div class="text-2xl font-bold {{ $valueColor }} mt-1">
                            @if($goal->direction === 'up')
                                ≥ {{ $goal->target_value }} {{ $goal->unit }} (Up)
                            @else
                                ≤ {{ $goal->target_value }} {{ $goal->unit }} (Down)
                            @endif
                        </div>

                        @if($statusData)
                            <p class="text-xs text-gray-600 mt-1">
                                Today: {{ round($current, 1) }} {{ $goal->unit }}
                                / {{ $goal->target_value }} {{ $goal->unit }}
                            </p>
                        @endif

                        <p class="text-sm text-gray-500 mt-2">
                            Status:
                            @if($status === 'on_track')
                                <span class="{{ $statusColor }} font-semibold">On Track</span>
                            @elseif($status === 'below')
                                <span class="{{ $statusColor }} font-semibold">Below Target</span>
                            @else
                                <span class="{{ $statusColor }} font-semibold">Over Limit</span>
                            @endif
                        </p>
                    </div>
                @endforeach
            @endif
        </div>
    </section>

    {{-- System Statistics --}}
    <section class="p-6 bg-white rounded-xl shadow-lg">
        <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">System Statistics</h2>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <div class="text-4xl font-extrabold text-indigo-600">{{ $totalRecipes }}</div>
                <div class="text-gray-500 mt-1">Total Recipes</div>
            </div>

            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <div class="text-4xl font-extrabold text-orange-600">{{ $scheduledMealsToday }}</div>
                <div class="text-gray-500 mt-1">Scheduled Meals Today</div>
            </div>

            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <div class="text-4xl font-extrabold text-pink-600">{{ $activeGoals->count() }}</div>
                <div class="text-gray-500 mt-1">Active Goals</div>
            </div>

            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <div class="text-4xl font-extrabold text-teal-600">{{ $biometricEntries }}</div>
                <div class="text-gray-500 mt-1">Biometric Entries</div>
            </div>
        </div>
    </section>

    {{-- Planned vs Logged Intake（更直观的进度条） --}}
    <section class="mt-8 p-6 bg-white rounded-xl shadow-lg">
        <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">
            Planned vs Logged Intake (Today)
        </h2>

        @php
            
    $nutrientsConfig = [
        'calories' => ['label' => 'Calories',         'unit' => 'kcal'],
        'protein'  => ['label' => 'Protein',          'unit' => 'g'],
        'carbs'    => ['label' => 'Carbs',            'unit' => 'g'],
        'fat'      => ['label' => 'Fat',              'unit' => 'g'],
        'co2'      => ['label' => 'Carbon footprint', 'unit' => 'kg CO₂e'],
    ];
@endphp

        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-1">Nutrient</th>
                    <th class="text-left py-1">Planned</th>
                    <th class="text-left py-1">Logged</th>
                    <th class="text-left py-1">Difference</th>
                    <th class="text-left py-1">Progress</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nutrientsConfig as $key => $meta)
                    @php
                        $planned  = $plannedTotals[$key] ?? 0;
                        $logged   = $totals[$key] ?? 0;
                        $diff     = $logged - $planned;
                        $percent  = $planned > 0 ? round(($logged / $planned) * 100) : null;

                        $barClass   = 'bg-green-500';
                        $statusText = 'On plan';

                        if ($percent !== null) {
                            if ($percent < 90) {
                                $barClass   = 'bg-yellow-500';
                                $statusText = 'Below plan';
                            } elseif ($percent > 110) {
                                $barClass   = 'bg-red-500';
                                $statusText = 'Above plan';
                            }
                        }
                    @endphp
                    <tr class="border-b">
                        <td class="py-2 font-medium text-gray-700">
                            {{ $meta['label'] }} ({{ $meta['unit'] }})
                        </td>
                        <td class="py-2">
                            {{ round($planned, 1) }}
                        </td>
                        <td class="py-2">
                            {{ round($logged, 1) }}
                        </td>
                        <td class="py-2">
                            {{ round($diff, 1) }}
                        </td>
                        <td class="py-2">
                            @if($planned > 0)
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $barClass }}"
                                         style="width: {{ max(5, min($percent, 150)) }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $percent }}% of plan · {{ $statusText }}
                                </div>
                            @else
                                <span class="text-xs text-gray-400">
                                    No plan for today
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    {{-- Today’s Meal Plan --}}
    <section class="mt-8 p-6 bg-white rounded-xl shadow-lg">
        <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Today’s Meal Plan</h2>

        @if($todayCalendarEntries->isEmpty())
            <p class="text-sm text-gray-500">No meals scheduled in your calendar for today.</p>
        @else
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-1">Meal</th>
                        <th class="text-left py-1">Recipe</th>
                        <th class="text-left py-1">Servings</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($todayCalendarEntries as $entry)
                    <tr class="border-b">
                        <td class="py-1 capitalize">{{ $entry->meal_type }}</td>
                        <td class="py-1">{{ $entry->recipe?->name ?? 'N/A' }}</td>
                        <td class="py-1">{{ $entry->servings }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </section>

    {{-- Today’s Actual Meals --}}
    <section class="mt-8 p-6 bg-white rounded-xl shadow-lg">
        <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Today’s Actual Meals</h2>

        @if($todayMealLogs->isEmpty())
            <p class="text-sm text-gray-500">No meals logged yet today.</p>
        @else
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-1">Time</th>
                        <th class="text-left py-1">Recipe</th>
                        <th class="text-left py-1">Servings</th>
                        <th class="text-left py-1">Calories</th>
                        <th class="text-left py-1">Protein</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($todayMealLogs as $log)
                    <tr class="border-b">
                        <td class="py-1">{{ $log->consumed_at?->format('H:i') }}</td>
                        <td class="py-1">{{ $log->recipe?->name ?? 'N/A' }}</td>
                        <td class="py-1">{{ $log->servings_consumed }}</td>
                        <td class="py-1">
                            @if($log->recipe)
                                {{ round($log->servings_consumed * $log->recipe->calories) }} kcal
                            @endif
                        </td>
                        <td class="py-1">
                            @if($log->recipe)
                                {{ round($log->servings_consumed * $log->recipe->protein, 1) }} g
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </section>

    {{-- Recommended recipes to reach goals --}}
    <section class="mt-8 p-6 bg-white rounded-xl shadow-lg">
        <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">
            Suggested Recipes to Help You Reach Today’s Goals
        </h2>

        @if($recommendedRecipes->isEmpty())
            <p class="text-sm text-gray-500">
                No specific suggestions right now. Log some meals or update your goals to get personalised ideas.
            </p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($recommendedRecipes as $recipe)
                    <div class="border rounded-lg p-3 bg-gray-50">
                        <div class="font-semibold text-gray-800 mb-1">
                            {{ $recipe->name }}
                        </div>
                        <p class="text-xs text-gray-500 mb-1">
                            Serving: {{ $recipe->serving_size }}
                        </p>
                        <p class="text-xs text-gray-600">
                            {{ $recipe->calories }} kcal ·
                            {{ $recipe->protein }} g protein ·
                            {{ $recipe->carbs }} g carbs ·
                            {{ $recipe->fat }} g fat
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Latest biometrics --}}
    @if($latestBiometric)
        <section class="mt-8 p-6 bg-white rounded-xl shadow-lg">
            <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Latest Health Metrics</h2>
            <p class="text-sm text-gray-700">
                Date: {{ $latestBiometric->measurement_date->format('Y-m-d') }}
            </p>
            @if($latestBiometric->weight)
                <p class="text-sm text-gray-700">
                    Weight: {{ $latestBiometric->weight }} kg
                </p>
            @endif
            @if($latestBiometric->systolic_bp && $latestBiometric->diastolic_bp)
                <p class="text-sm text-gray-700">
                    Blood Pressure: {{ $latestBiometric->systolic_bp }}/{{ $latestBiometric->diastolic_bp }} mmHg
                </p>
            @endif
            @if($latestBiometric->bmi)
                <p class="text-sm text-gray-700">
                    BMI: {{ $latestBiometric->bmi }}
                </p>
            @endif
        </section>
    @endif

    {{-- Add Recipe Button --}}
    <div class="mt-8 text-center">
        <a href="{{ route('recipes.create') }}"
           class="inline-block px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition duration-150">
            Add New Recipe
        </a>
    </div>
</main>

</body>
</html>
