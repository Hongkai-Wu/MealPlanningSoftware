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

<main class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Biometric measurements</h1>
        <a href="{{ route('biometrics.create') }}"
           class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-700">
            Add measurement
        </a >
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-2 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <section class="bg-white rounded-xl shadow p-4">
        @if($biometrics->isEmpty())
            <p class="text-sm text-gray-500">
                You have no biometric measurements yet. Click “Add measurement” to create one.
            </p >
        @else
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Date</th>
                        <th class="text-left py-2">Weight (kg)</th>
                        <th class="text-left py-2">Blood pressure</th>
                        <th class="text-left py-2">BMI</th>
                        <th class="text-left py-2">Notes</th>
                        <th class="text-left py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($biometrics as $item)
                    <tr class="border-b">
                        <td class="py-2">
                            {{ $item->measurement_date?->format('Y-m-d') }}
                        </td>
                        <td class="py-2">
                            {{ $item->weight !== null ? $item->weight : '-' }}
                        </td>
                        <td class="py-2">
                            @if($item->systolic_bp && $item->diastolic_bp)
                                {{ $item->systolic_bp }}/{{ $item->diastolic_bp }} mmHg
                            @else
                                -
                            @endif
                        </td>
                        <td class="py-2">
                            {{ $item->bmi !== null ? $item->bmi : '-' }}
                        </td>
                        <td class="py-2 max-w-xs">
                            <span class="block truncate" title="{{ $item->notes }}">
                                {{ $item->notes ?? '-' }}
                            </span>
                        </td>
                        <td class="py-2 space-x-2">
                            <a href="{{ route('biometrics.edit', $item) }}"
                               class="text-blue-600 hover:underline text-xs">
                                Edit
                            </a >
                            <form action="{{ route('biometrics.destroy', $item) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Delete this measurement?');">
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

            <div class="mt-4">
                {{ $biometrics->links() }}
            </div>
        @endif
    </section>

</main>

</body>
</html>