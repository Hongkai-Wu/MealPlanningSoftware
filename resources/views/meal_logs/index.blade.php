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

<main class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Meal Logs</h1>
            <a href="{{ route('meal_logs.create') }}"
               class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-700">
                Add meal log
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 px-3 py-2 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($mealLogs->isEmpty())
            <p class="text-sm text-gray-500">You have no meal logs yet.</p>
        @else
            <table class="min-w-full text-sm">
                <thead>
                <tr class="border-b">
                    <th class="text-left py-2">Time</th>
                    <th class="text-left py-2">Recipe</th>
                    <th class="text-left py-2">Servings</th>
                    <th class="text-left py-2">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($mealLogs as $log)
                    <tr class="border-b">
                        <td class="py-2">
                            {{ $log->consumed_at?->format('Y-m-d H:i') }}
                        </td>
                        <td class="py-2">
                            {{ $log->recipe?->name ?? 'N/A' }}
                        </td>
                        <td class="py-2">
                            {{ $log->servings_consumed }}
                        </td>
                        <td class="py-2">
                            <a href="{{ route('meal_logs.edit', $log) }}"
                               class="text-blue-600 hover:underline text-xs mr-3">
                                Edit
                            </a>
                            <form action="{{ route('meal_logs.destroy', $log) }}"
                                  method="POST"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-xs text-red-600 hover:underline"
                                        onclick="return confirm('Delete this log?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</main>

</body>
</html>
