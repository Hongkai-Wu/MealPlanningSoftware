<!-- Using the layouts.app template -->
@extends('layouts.app')

<!-- Set the page title -->
@section('title', 'Edit Meal Log')

<!-- Page main content -->
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-2">
            Edit Meal Log
        </h1>

        <!-- Form with CSRF protection and PUT method spoofing -->
        <form action="{{ route('meal_logs.update', $mealLog) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Recipe Selection -->
            <div class="mb-4">
                <label for="recipe_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Select Recipe:
                </label>
                <select 
                    name="recipe_id" 
                    id="recipe_id" 
                    required 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white p-3 text-lg"
                    onchange="updateCalories(this)"
                >
                    <option value="">-- Select a Recipe --</option>
                    @foreach ($recipes as $recipe)
                        <option 
                            value="{{ $recipe->id }}" 
                            data-calories="{{ $recipe->calories_kcal }}"
                            {{ $recipe->id == old('recipe_id', $mealLog->recipe_id) ? 'selected' : '' }}
                        >
                            {{ $recipe->name }} ({{ $recipe->calories_kcal }} kcal/serving)
                        </option>
                    @endforeach
                </select>
                @error('recipe_id')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Real-time Calorie Display -->
            <div id="calorie_display" class="bg-green-50 dark:bg-indigo-900/30 p-3 rounded-md mb-6 text-sm text-green-700 dark:text-indigo-300">
                Calories per serving for selected recipe: <span id="calories_per_serving" class="font-semibold">
                    <!-- Initial value, displays calories if mealLog has a linked recipe -->
                    @if($mealLog->recipe)
                        {{ $mealLog->recipe->calories_kcal }}
                    @else
                        N/A
                    @endif
                </span> kcal
            </div>
            
            <!-- Servings Consumed -->
            <div class="mb-4">
                <label for="servings_consumed" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Servings Consumed:
                </label>
                <input 
                    type="number" 
                    name="servings_consumed" 
                    id="servings_consumed" 
                    step="0.1" 
                    min="0.1" 
                    required 
                    value="{{ old('servings_consumed', $mealLog->servings_consumed) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white p-3 text-lg"
                >
                @error('servings_consumed')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Time Consumed -->
            <div class="mb-6">
                <label for="consumed_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Time Consumed:
                </label>
                <!-- Pre-fill using Carbon to format to YYYY-MM-DDTHH:MM format required by input[type=datetime-local] -->
                <input 
                    type="datetime-local" 
                    name="consumed_at" 
                    id="consumed_at" 
                    required 
                    value="{{ old('consumed_at', \Carbon\Carbon::parse($mealLog->consumed_at)->format('Y-m-d\TH:i')) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white p-3 text-lg"
                >
                @error('consumed_at')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-between items-center">
                <button 
                    type="submit" 
                    class="flex-1 mr-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition duration-150 ease-in-out"
                >
                    Update Log
                </button>
                <a href="{{ route('meal_logs.index') }}" class="flex-1 ml-2 text-center px-4 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold rounded-lg shadow-md transition duration-150 ease-in-out">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript function to update calorie display -->
<script>
    function updateCalories(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const calories = selectedOption.getAttribute('data-calories');
        const caloriesDisplay = document.getElementById('calories_per_serving');
        
        if (calories) {
            caloriesDisplay.textContent = calories;
        } else {
            caloriesDisplay.textContent = 'N/A';
        }
    }

    // Call once after the page loads to ensure initial calorie display is correct
    document.addEventListener('DOMContentLoaded', () => {
        const selectElement = document.getElementById('recipe_id');
        if (selectElement) {
            updateCalories(selectElement);
        }
    });
</script>
@endsection