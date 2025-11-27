<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Goal | Meal Planning Software</title>
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

<main class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Edit goal</h1>

        <form action="{{ route('goals.update', $goal) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Goal type
                    </label>
                    <select name="goal_type"
                            class="w-full border rounded px-2 py-1 text-sm"
                            required>
                        @foreach($goalTypes as $key => $label)
                            <option value="{{ $key }}"
                                {{ $goal->goal_type === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
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
                        <option value="up" {{ $goal->direction === 'up' ? 'selected' : '' }}>Increase (≥)</option>
                        <option value="down" {{ $goal->direction === 'down' ? 'selected' : '' }}>Limit (≤)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Target value
                    </label>
                    <input type="number" step="0.1" name="target_value"
                           value="{{ $goal->target_value }}"
                           class="w-full border rounded px-2 py-1 text-sm"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Unit
                    </label>
                    <input type="text" name="unit"
                           value="{{ $goal->unit }}"
                           class="w-full border rounded px-2 py-1 text-sm"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Start date
                    </label>
                    <input type="date" name="start_date"
                           value="{{ $goal->start_date }}"
                           class="w-full border rounded px-2 py-1 text-sm"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        End date (optional)
                    </label>
                    <input type="date" name="end_date"
                           value="{{ $goal->end_date }}"
                           class="w-full border rounded px-2 py-1 text-sm">
                </div>
            </div>

            <div class="flex items-center mt-2">
                <input type="checkbox" id="is_active" name="is_active"
                       class="mr-2"
                       {{ $goal->is_active ? 'checked' : '' }}>
                <label for="is_active" class="text-sm text-gray-700">
                    Active
                </label>
            </div>

            <div class="flex items-center space-x-3 mt-4">
                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-700">
                    Save changes
                </button>

                <a href="{{ route('goals.index') }}"
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
