@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
  <div class="max-w-4xl mx-auto">

    @if(session('success'))
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <span class="block sm:inline">{{ session('success') }}</span>
      </div>
    @endif
    @if(session('error'))
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <span class="block sm:inline">{{ session('error') }}</span>
      </div>
    @endif

    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">Meal Logs</h1>
      <a href="{{ route('meal_logs.create') }}"
   class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
  Log a Meal
</a>

    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-8">
      <div class="bg-white shadow rounded p-4">
        <h2 class="font-semibold mb-3">Today’s Totals</h2>
        <ul class="space-y-1 text-sm">
          <li>Calories: {{ $todayNutrition['calories'] ?? 0 }} kcal</li>
          <li>Protein: {{ $todayNutrition['protein'] ?? 0 }} g</li>
          <li>Carbs: {{ $todayNutrition['carbs'] ?? 0 }} g</li>
          <li>Fat: {{ $todayNutrition['fat'] ?? 0 }} g</li>
          <li>Fiber: {{ $todayNutrition['fiber'] ?? 0 }} g</li>
          <li>Sugar: {{ $todayNutrition['sugar'] ?? 0 }} g</li>
          <li>Sodium: {{ $todayNutrition['sodium'] ?? 0 }} mg</li>
        </ul>
      </div>

      <div class="bg-white shadow rounded p-4">
        <h2 class="font-semibold mb-3">Goal Status</h2>
        @forelse($goalsResult as $g)
          <div class="flex justify-between border-b py-1 text-sm">
            <span>{{ strtoupper($g['metric']) }} ({{ $g['direction']==='up' ? '≥' : '≤' }} {{ $g['target'] }} {{ $g['unit'] }})</span>
            <span>
              {{ $g['current'] }} {{ $g['unit'] }} ·
              @if($g['status']==='ok')
                <span class="text-green-600">OK</span>
              @elseif($g['status']==='deficit')
                <span class="text-yellow-600">-{{ $g['delta'] }} {{ $g['unit'] }}</span>
              @else
                <span class="text-red-600">+{{ $g['delta'] }} {{ $g['unit'] }}</span>
              @endif
            </span>
          </div>
        @empty
          <em class="text-gray-500 text-sm">No goals set.</em>
        @endforelse
      </div>
    </div>

    <div class="bg-white shadow rounded">
      <div class="p-4 border-b">
        <h2 class="font-semibold">All Logs</h2>
      </div>
      <div class="p-4">
        @if ($entries->isEmpty())
          <em class="text-gray-500">No logs yet.</em>
        @else
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="text-left border-b">
                  <th class="py-2 pr-3">Date</th>
                  <th class="py-2 pr-3">Meal</th>
                  <th class="py-2 pr-3">Recipe</th>
                  <th class="py-2 pr-3">Servings</th>
                  <th class="py-2 pr-3">Calories</th>
                  <th class="py-2 pr-3">Protein</th>
                  <th class="py-2 pr-3">Carbs</th>
                  <th class="py-2 pr-3">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($entries as $e)
                  @php($n = $e->nutrition)
                  <tr class="border-b">
                    <td class="py-2 pr-3">{{ $e->date->toDateString() }}</td>
                    <td class="py-2 pr-3">{{ $e->meal_type }}</td>
                    <td class="py-2 pr-3">{{ $e->recipe->name ?? 'Unknown' }}</td>
                    <td class="py-2 pr-3">{{ $e->servings }}</td>
                    <td class="py-2 pr-3">{{ $n['calories'] }}</td>
                    <td class="py-2 pr-3">{{ $n['protein'] }}</td>
                    <td class="py-2 pr-3">{{ $n['carbs'] }}</td>
                    <td class="py-2 pr-3">
                      <form method="POST" action="{{ route('meal_logs.destroy', $e) }}"
                            onsubmit="return confirm('Delete this log?')">
                        @csrf @method('DELETE')
                        <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>

  </div>
</div>
@endsection