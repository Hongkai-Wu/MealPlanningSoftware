@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl mx-auto bg-white p-8 rounded-xl shadow-lg">
        <a href=" 'meal_logs.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150 ease-in-out flex items-center mb-6">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Log List
        </a >

        <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-3">Log a Meal</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Whoops!</strong>
                <span class="block sm:inline">There were some problems with your input.</span>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('meal_logs.store') }}">
            @csrf

            <div class="mb-5">
                <label for="recipe_id" class="block text-sm font-medium text-gray-700 mb-2">Which recipe did you eat?</label>
                <select id="recipe_id" name="recipe_id" required
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">-- Please select a recipe --</option>
                    @foreach ($recipes as $recipe)
                        <option value="{{ $recipe->id }}" {{ old('recipe_id') == $recipe->id ? 'selected' : '' }}>
                            {{ $recipe->name }} ({{ $recipe->calories }} kcal)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-5">
                <label for="servings" class="block text-sm font-medium text-gray-700 mb-2">How many servings did you consume?</label>
                <input type="number" id="servings" name="servings" min="1" step="1" required value="{{ old('servings', 1) }}"
                       class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <p class="mt-2 text-xs text-gray-500">Enter an integer number of servings (e.g., 1, 2).</p >
            </div>

            <div class="mb-5">
                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <input type="date" id="date" name="date" required value="{{ old('date', now()->toDateString()) }}"
                       class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div class="mb-6">
                <label for="meal_type" class="block text-sm font-medium text-gray-700 mb-2">Meal Type</label>
                <select id="meal_type" name="meal_type" required
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @php $mt = old('meal_type'); @endphp
                    <option value="">-- Select meal type --</option>
                    <option {{ $mt==='Breakfast' ? 'selected' : '' }}>Breakfast</option>
                    <option {{ $mt==='Lunch' ? 'selected' : '' }}>Lunch</option>
                    <option {{ $mt==='Dinner' ? 'selected' : '' }}>Dinner</option>
                    <option {{ $mt==='Snack' ? 'selected' : '' }}>Snack</option>
                </select>
            </div>

            <div>
                <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                    Confirm Log
                </button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  
  const dateInput = document.querySelector('input[name="date"]');
  if (dateInput) dateInput.max = new Date().toISOString().split('T')[0];
  
  const form = document.querySelector('form');
  if (form) {
   form.addEventListener('submit', e => {
  const btn = form.querySelector('button[type="submit"]');
  if (btn) {
    btn.disabled = true;
    btn.innerText = 'Submitting...';
  }

  
  e.preventDefault();
  setTimeout(() => form.submit(), 2000);
});  }
});
</script>
@endsection