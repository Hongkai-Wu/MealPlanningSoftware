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
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Name</th>
                        <th class="text-left py-2">Serving</th>
                        <th class="text-left py-2">Calories</th>
                        <th class="text-left py-2">Protein</th>
                        <th class="text-left py-2">Carbs</th>
                        <th class="text-left py-2">Fat</th>
                        <th class="text-left py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($recipes as $recipe)
                    <tr class="border-b">
                        <td class="py-2 font-semibold">{{ $recipe->name }}</td>
                        <td class="py-2">{{ $recipe->serving_size }}</td>
                        <td class="py-2">{{ $recipe->calories }} kcal</td>
                        <td class="py-2">{{ $recipe->protein }} g</td>
                        <td class="py-2">{{ $recipe->carbs }} g</td>
                        <td class="py-2">{{ $recipe->fat }} g</td>
                        <td class="py-2 space-x-2">
                            <a href="{{ route('recipes.edit', $recipe) }}"
                               class="text-blue-600 hover:underline text-xs">
                                Edit
                            </a>
                            <form action="{{ route('recipes.destroy', $recipe) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Delete this recipe?');">
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
    </section>
</main>

</body>
</html>
