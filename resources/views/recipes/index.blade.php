<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Recipes | Food Tracker</title>
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

@include('partials.navbar', ['active' => 'recipes'])

<main class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <h1 class="text-3xl font-bold text-gray-800">My Recipes</h1>
            <a href="{{ route('dashboard') }}"
               class="px-3 py-1 bg-gray-200 text-sm rounded-lg hover:bg-gray-300">
                Back to dashboard
            </a>
        </div>
        <a href="{{ route('recipes.create') }}"
           class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-700">
            Add new recipe
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-2 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <section class="bg-white rounded-xl shadow p-4">
        @if($recipes->isEmpty())
            <p class="text-sm text-gray-500">You have no recipes yet. Click “Add new recipe” to create one.</p>
        @else
             <table class="min-w-full bg-white rounded-lg overflow-hidden">
    <thead class="bg-gray-100">
        <tr>
            <th class="py-2 px-3 text-left">Name</th>
            <th class="py-2 px-3 text-left">Serving</th>
            <th class="py-2 px-3 text-left">Calories</th>
            <th class="py-2 px-3 text-left">Protein</th>
            <th class="py-2 px-3 text-left">Carbs</th>
            <th class="py-2 px-3 text-left">Fiber (g)</th>
            <th class="py-2 px-3 text-left">Fat</th>
            <th class="py-2 px-3 text-left">CO₂ (kg)</th>
            <th class="py-2 px-3 text-left">Actions</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($recipes as $recipe)
            <tr class="border-b">
                <td class="py-2 px-3">{{ $recipe->name }}</td>
                <td class="py-2 px-3">{{ $recipe->serving_size }}</td>
                <td class="py-2 px-3">{{ $recipe->calories }} kcal</td>
                <td class="py-2 px-3">{{ $recipe->protein }} g</td>
                <td class="py-2 px-3">{{ $recipe->carbs }} g</td>
                <td class="py-2 px-3">{{ $recipe->fiber }} g</td>
                <td class="py-2 px-3">{{ $recipe->fat }} g</td>

                <td class="py-2 px-3">
                    {{ $recipe->carbonFootprint->co2_emissions ?? '-' }}
                </td>

                <td class="py-2 px-3">
                    <a href="{{ route('recipes.edit', $recipe->id) }}" class="text-blue-600">Edit</a>
                    <a href="{{ route('recipes.destroy', $recipe->id) }}"
                       onclick="return confirm('Delete this recipe?')"
                       class="text-red-600 ml-2">Delete</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
       @endif
    </section>
</main>

</body>
</html>
