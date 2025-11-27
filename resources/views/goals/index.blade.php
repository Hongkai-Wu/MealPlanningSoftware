<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Goals | Meal Planning Software</title>
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

@include('partials.navbar', ['active' => 'goals'])

<main class="max-w-5xl mx-auto space-y-8">

    <section>
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-bold text-gray-800">My Goals</h1>
            <a href="{{ route('dashboard') }}"
               class="px-3 py-1 bg-gray-200 text-sm rounded-lg hover:bg-gray-300">
                Back to dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-2 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- 目标列表 --}}
        <div class="bg-white rounded-xl shadow p-4 mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-3 border-b pb-2">Current goals</h2>

            @if($goals->isEmpty())
                <p class="text-sm text-gray-500">You have no goals yet. Use the form below to create one.</p>
            @else
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="border-b">
                        <th class="text-left py-1">Type</th>
                        <th class="text-left py-1">Direction</th>
                        <th class="text-left py-1">Target</th>
                        <th class="text-left py-1">Period</th>
                        <th class="text-left py-1">Active</th>
                        <th class="text-left py-1">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($goals as $goal)
                        <tr class="border-b">
                            <td class="py-1">
                                {{ $goalTypes[$goal->goal_type] ?? ucfirst($goal->goal_type) }}
                            </td>
                            <td class="py-1">
                                {{ $goal->direction === 'up' ? 'Increase (≥)' : 'Limit (≤)' }}
                            </td>
                            <td class="py-1">
                                {{ $goal->target_value }} {{ $goal->unit }}
                            </td>
                            <td class="py-1">
                                {{ $goal->start_date }}
                                @if($goal->end_date)
                                    – {{ $goal->end_date }}
                                @else
                                    – open-ended
                                @endif
                            </td>
                            <td class="py-1">
                                @if($goal->is_active)
                                    <span class="text-green-600 font-semibold">Yes</span>
                                @else
                                    <span class="text-gray-400">No</span>
                                @endif
                            </td>
                            <td class="py-1 space-x-2">
                                <a href="{{ route('goals.edit', $goal) }}"
                                   class="text-blue-600 hover:underline text-xs">
                                    Edit
                                </a>

                                <form action="{{ route('goals.destroy', $goal) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Delete this goal?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:underline text-xs">
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

        {{-- 新建目标表单 --}}
        <div class="bg-white rounded-xl shadow p-4">
            <h2 class="text-xl font-semibold text-gray-700 mb-3 border-b pb-2">Create a new goal</h2>

            <form action="{{ route('goals.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Goal type
                        </label>
                        <select name="goal_type"
                                class="w-full border rounded px-2 py-1 text-sm"
                                required>
                            @foreach($goalTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Direction
                        </label>
                        <select name="direction"
                                class="w-full border rounded px-2 py-1 text-sm"
                                required>
                            <option value="up">Increase (≥)</option>
                            <option value="down">Limit (≤)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Target value
                        </label>
                        <input type="number" step="0.1" name="target_value"
                               class="w-full border rounded px-2 py-1 text-sm"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Unit (e.g. kcal, g, kg CO₂e)
                        </label>
                        <input type="text" name="unit"
                               class="w-full border rounded px-2 py-1 text-sm"
                               placeholder="kcal, g, kg CO₂e"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Start date
                        </label>
                        <input type="date" name="start_date"
                               value="{{ now()->toDateString() }}"
                               class="w-full border rounded px-2 py-1 text-sm"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            End date (optional)
                        </label>
                        <input type="date" name="end_date"
                               class="w-full border rounded px-2 py-1 text-sm">
                    </div>
                </div>

                <div class="flex items-center mt-2">
                    <input type="checkbox" id="is_active" name="is_active" class="mr-2" checked>
                    <label for="is_active" class="text-sm text-gray-700">
                        Active
                    </label>
                </div>

                <div>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-700">
                        Save goal
                    </button>
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
    </section>
</main>

</body>
</html>
