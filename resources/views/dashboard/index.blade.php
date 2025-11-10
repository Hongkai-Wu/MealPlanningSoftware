<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} | Food Goals Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
        }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8">

    <!-- Header & Navigation -->
    <header class="mb-8">
        <nav class="flex justify-between items-center bg-white p-4 rounded-xl shadow-md">
            <div class="text-2xl font-bold text-green-700">
                Food Tracker
            </div>
            <div class="flex space-x-4">
                <!-- Current Page: Dashboard -->
                <a href="/" class="text-green-700 font-semibold border-b-2 border-green-500 pb-1">Dashboard</a>
                <!-- Link to Recipes (will be implemented next) -->
                <a href="{{ url('recipes') }}" class="text-gray-600 hover:text-green-700 font-semibold">Recipes</a>
                <!-- Link to Meal Plan (later step) -->
                <a href="/meal-plan" class="text-gray-600 hover:text-green-700 font-semibold">Meal Plan</a>
                <!-- Link to Biometrics (later step) -->
                <a href="/biometrics" class="text-gray-600 hover:text-green-700 font-semibold">Biometrics</a>
            </div>
        </nav>
    </header>

    <main>
        <h1 class="text-3xl font-bold text-gray-800 mb-6">{{ $title ?? 'Dashboard' }}</h1>

        <!-- Goal Status Section -->
        <section class="mb-8 p-6 bg-white rounded-xl shadow-lg">
            <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Goal Status Overview</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Card 1: Calories Goal -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="text-sm font-medium text-blue-600">Daily Calories Goal</div>
                    <div class="text-2xl font-bold text-blue-800 mt-1">
                        1800 kcal (Down)
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Status: <span class="text-green-600 font-semibold">On Track</span></p>
                </div>

                <!-- Card 2: Protein Goal -->
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <div class="text-sm font-medium text-yellow-600">Daily Protein Goal</div>
                    <div class="text-2xl font-bold text-yellow-800 mt-1">
                        80 g (Up)
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Status: <span class="text-red-600 font-semibold">Low Intake</span></p>
                </div>

                <!-- Card 3: Carbon Footprint Goal -->
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <div class="text-sm font-medium text-green-600">Weekly Carbon Footprint</div>
                    <div class="text-2xl font-bold text-green-800 mt-1">
                        12 kg CO2e (Down)
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Status: <span class="text-blue-600 font-semibold">Meeting Goal</span></p>
                </div>

            </div>
        </section>

        <!-- Stats Section -->
        <section class="p-6 bg-white rounded-xl shadow-lg">
            <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">System Statistics</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

                <!-- Stat 1: Total Recipes -->
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <div class="text-4xl font-extrabold text-indigo-600">{{ $totalRecipes ?? 0 }}</div>
                    <div class="text-gray-500 mt-1">Total Recipes</div>
                </div>

                <!-- Stat 2: Scheduled Meals Today -->
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <div class="text-4xl font-extrabold text-orange-600">{{ $scheduledMealsToday ?? 0 }}</div>
                    <div class="text-gray-500 mt-1">Scheduled Meals Today</div>
                </div>
                
                <!-- Stat 3: Active Goals -->
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <div class="text-4xl font-extrabold text-pink-600">{{ $activeGoals ?? 3 }}</div>
                    <div class="text-gray-500 mt-1">Active Goals</div>
                </div>
                
                <!-- Stat 4: Biometric Entries -->
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <div class="text-4xl font-extrabold text-teal-600">{{ $biometricEntries ?? 0 }}</div>
                    <div class="text-gray-500 mt-1">Biometric Entries</div>
                </div>

            </div>
        </section>
        
        <!-- Action Button Placeholder -->
        <div class="mt-8 text-center">
             <a href="{{ url('recipes/create') }}" class="inline-block px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition duration-150">
                Add New Recipe
            </a>
        </div>
    </main>

</body>
</html>