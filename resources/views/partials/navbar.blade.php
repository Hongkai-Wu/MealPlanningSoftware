<header class="mb-8">
    <nav class="flex justify-between items-center bg-white p-4 rounded-xl shadow-md">
        <div class="text-2xl font-bold text-green-700">
            Meal Planning Software
        </div>

        <div class="flex space-x-4">
            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
               class="{{ ($active ?? '') === 'dashboard'
                        ? 'text-green-700 font-semibold border-b-2 border-green-500 pb-1'
                        : 'text-gray-600 hover:text-green-700 font-semibold' }}">
                Dashboard
            </a>

            {{-- Recipes --}}
            <a href="{{ route('recipes.index') }}"
               class="{{ ($active ?? '') === 'recipes'
                        ? 'text-green-700 font-semibold border-b-2 border-green-500 pb-1'
                        : 'text-gray-600 hover:text-green-700 font-semibold' }}">
                Recipes
            </a>

            {{-- Goals --}}
            <a href="{{ route('goals.index') }}"
               class="{{ ($active ?? '') === 'goals'
                        ? 'text-green-700 font-semibold border-b-2 border-green-500 pb-1'
                        : 'text-gray-600 hover:text-green-700 font-semibold' }}">
                Goals
            </a>

            {{-- Meal Logs --}}
            <a href="{{ route('meal_logs.index') }}"
               class="{{ ($active ?? '') === 'meal_logs'
                        ? 'text-green-700 font-semibold border-b-2 border-green-500 pb-1'
                        : 'text-gray-600 hover:text-green-700 font-semibold' }}">
                Meal Logs
            </a>

            {{-- Meal Plan --}}
            <a href="{{ route('meal_plan.index') }}"
               class="{{ ($active ?? '') === 'meal_plan'
                        ? 'text-green-700 font-semibold border-b-2 border-green-500 pb-1'
                        : 'text-gray-600 hover:text-green-700 font-semibold' }}">
                Meal Plan
            </a>

            {{-- Biometrics --}}
            <a href="{{ route('biometrics.index') }}"
               class="{{ ($active ?? '') === 'biometrics'
                        ? 'text-green-700 font-semibold border-b-2 border-green-500 pb-1'
                        : 'text-gray-600 hover:text-green-700 font-semibold' }}">
                Biometrics
            </a>
        </div>

        <div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="px-3 py-1 rounded-lg bg-red-100 text-red-700 text-sm font-semibold hover:bg-red-200">
                    Logout
                </button>
            </form>
        </div>
    </nav>
</header>
