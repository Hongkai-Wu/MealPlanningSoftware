<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Recipes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f7f9fb; }
    </style>
</head>
<body class="p-4 md:p-8">
    <div class="max-w-4xl mx-auto bg-white shadow-xl rounded-2xl p-6 md:p-10">

        <div class="flex justify-between items-center mb-6 border-b pb-2">
            <h1 class="text-3xl font-bold text-gray-800">My Recipes</h1>
            <a href="{{ route('recipes.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Add New Recipe
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($recipes->isEmpty())
            <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Recipes Found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Click the button above to start creating your recipes.
                </p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($recipes as $recipe)
                    <div class="bg-white p-5 rounded-xl border border-gray-200 flex items-start justify-between shadow-md hover:shadow-lg transition duration-200">
                        <!-- Recipe Details -->
                        <div class="flex-1 min-w-0 pr-4">
                            <p class="text-xl font-semibold text-gray-900 truncate" title="{{ $recipe->name }}">
                                {{ $recipe->name }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                Calories per Serving: 
                                <span class="font-bold text-orange-600">{{ number_format($recipe->calories_kcal, 0) }} kcal</span>
                            </p>
                            @if ($recipe->description)
                                <p class="text-xs text-gray-500 mt-2 line-clamp-2">
                                    {{ $recipe->description }}
                                </p>
                            @endif
                        </div>
                        
                        <!-- Actions (Edit and Delete) -->
                        <div class="flex space-x-2 items-center flex-shrink-0">
                            <!-- Edit Button -->
                            <a href="{{ route('recipes.edit', $recipe) }}" 
                               class="text-sm text-indigo-600 hover:text-indigo-900 font-medium py-1 px-3 bg-indigo-50 rounded-lg transition duration-150">
                                Edit
                            </a>

                            <!-- Delete Form -->
                            <!-- NOTE: We use a simple confirm dialogue for deletion as a temporary solution. -->
                            <form action="{{ route('recipes.destroy', $recipe) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the recipe: {{ $recipe->name }}? This cannot be undone.');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-sm text-red-600 hover:text-red-900 font-medium py-1 px-3 bg-red-50 rounded-lg transition duration-150">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        
    </div>
</body>
</html>