<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biometrics | Food Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8">

@include('partials.navbar', ['active' => 'biometrics'])

<main class="max-w-5xl mx-auto">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Biometric measurements</h1>

            <a href="{{ route('biometrics.create') }}"
               class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-700">
                Add measurement
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 px-3 py-2 rounded">
                {{ session('success') }}
            </div>
        @endif

        @php
          
            $sorted = $measurements->sortBy('measurement_date');

            $chartLabels = $sorted->map(function ($row) {
                return $row->measurement_date
                    ? $row->measurement_date->format('Y-m-d')
                    : null;
            })->filter()->values();

            $chartWeights = $sorted->map(function ($row) {
                return $row->weight;
            })->filter()->values();

           
            $chartBmis = $sorted->map(function ($row) {
                return $row->bmi ?? null;
            })->values();
        @endphp

      
        @if($chartLabels->count() >= 2)
            <section class="mb-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Weight &amp; BMI trend over time</h2>
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                    <canvas id="weightChart" height="80"></canvas>
                </div>
            </section>
        @endif

       
        @if($measurements->isEmpty())
            <p class="text-sm text-gray-500">No biometric measurements yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="text-left py-2 px-2">Date</th>
                        <th class="text-left py-2 px-2">Weight (kg)</th>
                        <th class="text-left py-2 px-2">Blood pressure</th>
                        <th class="text-left py-2 px-2">BMI</th>
                        <th class="text-left py-2 px-2">Notes</th>
                        <th class="text-left py-2 px-2">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($measurements as $m)
                        <tr class="border-b">
                            <td class="py-2 px-2">
                                {{ $m->measurement_date ? $m->measurement_date->format('Y-m-d') : '-' }}
                            </td>
                            <td class="py-2 px-2">
                                {{ $m->weight !== null ? $m->weight : '-' }}
                            </td>
                            <td class="py-2 px-2">
                                @if($m->systolic_bp && $m->diastolic_bp)
                                    {{ $m->systolic_bp }}/{{ $m->diastolic_bp }} mmHg
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-2 px-2">
                                {{ $m->bmi !== null ? $m->bmi : '-' }}
                            </td>
                            <td class="py-2 px-2">
                                {{ $m->notes ?: '-' }}
                            </td>
                            <td class="py-2 px-2">
                                <a href="{{ route('biometrics.edit', $m) }}"
                                   class="text-xs text-blue-600 hover:underline mr-2">Edit</a>
                                <form action="{{ route('biometrics.destroy', $m) }}"
                                      method="POST"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs text-red-600 hover:underline"
                                            onclick="return confirm('Delete this measurement?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</main>


@if($chartLabels->count() >= 2)
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels     = @json($chartLabels->toArray());
        const weightData = @json($chartWeights->toArray());
        const bmiData    = @json($chartBmis->toArray());

        const ctx = document.getElementById('weightChart').getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Weight (kg)',
                        data: weightData,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.15)',
                        tension: 0.2,
                        pointRadius: 3,
                        yAxisID: 'y-weight',
                    },
                    {
                        label: 'BMI',
                        data: bmiData,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.15)',
                        tension: 0.2,
                        pointRadius: 3,
                        yAxisID: 'y-bmi',
                    },
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    },
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    'y-weight': {
                        type: 'linear',
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Weight (kg)',
                        },
                    },
                    'y-bmi': {
                        type: 'linear',
                        position: 'right',
                        title: {
                            display: true,
                            text: 'BMI',
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date',
                        },
                    },
                },
            }
        });
    </script>
@endif

</body>
</html>
