<!-- Using the layouts.app template -->
@extends('layouts.app')

<!-- Set the page title -->
@section('title', 'Add New Recipe')

<!-- Page main content -->
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-2">
            Add New Recipe
        </h1>

        <!-- Form with CSRF protection -->
        <form action="{{ route('recipes.store') }}" method="POST">
            @csrf

            <!-- Recipe Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Recipe Name:
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    required 
                    maxlength="255"
                    value="{{ old('name') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white p-3 text-lg"
                    placeholder="e.g., Grilled Chicken Salad"
                >
                @error('name')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Calories per serving (kcal) -->
            <div class="mb-4">
                <label for="calories_kcal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Calories per Serving (kcal):
                </label>
                <input 
                    type="number" 
                    name="calories_kcal" 
                    id="calories_kcal" 
                    required 
                    min="1"
                    value="{{ old('calories_kcal') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white p-3 text-lg"
                    placeholder="e.g., 350"
                >
                @error('calories_kcal')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description (Optional):
                </label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white p-3 text-lg"
                    placeholder="Briefly describe the recipe or ingredients."
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-between items-center">
                <button 
                    type="submit" 
                    class="flex-1 mr-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition duration-150 ease-in-out"
                >
                    Save Recipe
                </button>
                <a href="{{ route('recipes.index') }}" class="flex-1 ml-2 text-center px-4 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold rounded-lg shadow-md transition duration-150 ease-in-out">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection