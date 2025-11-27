<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add measurement | Food Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8">

@include('partials.navbar', ['active' => 'biometrics'])
<main class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Add biometric measurement</h1>

        <form action="{{ route('biometrics.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Date
                </label>
                <input type="date" name="measurement_date"
                       value="{{ old('measurement_date', $defaultDate) }}"
                       class="w-full border rounded px-2 py-1 text-sm"
                       required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Weight (kg)
                    </label>
                    <input type="number" step="0.1" min="0" name="weight"
                           value="{{ old('weight') }}"
                           class="w-full border rounded px-2 py-1 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        BMI
                    </label>
                    <input type="number" step="0.1" min="0" name="bmi"
                           value="{{ old('bmi') }}"
                           class="w-full border rounded px-2 py-1 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Systolic BP (high, mmHg)
                    </label>
                    <input type="number" min="0" name="systolic_bp"
                           value="{{ old('systolic_bp') }}"
                           class="w-full border rounded px-2 py-1 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Diastolic BP (low, mmHg)
                    </label>
                    <input type="number" min="0" name="diastolic_bp"
                           value="{{ old('diastolic_bp') }}"
                           class="w-full border rounded px-2 py-1 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Notes (optional)
                </label>
                <textarea name="notes" rows="3"
                          class="w-full border rounded px-2 py-1 text-sm">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center space-x-3 mt-4">
                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-700">
                    Save
                </button>
                <a href="{{ route('biometrics.index') }}"
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