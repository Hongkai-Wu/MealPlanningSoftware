<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Planning Software - Dashboard</title>
    <!-- Use Tailwind CSS for quick styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Ensure the page takes full height and is centered */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a; /* Dark background */
            color: #f8fafc; /* Light text */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="max-w-4xl w-full p-8 bg-gray-800 rounded-xl shadow-2xl">
        <header class="text-center mb-10">
            <h1 class="text-5xl font-extrabold text-indigo-400 mb-2">
                Meal Planning Software
            </h1>
            <p class="text-xl text-gray-400">Your Healthy Eating Partner</p>
        </header>

        <!-- SUCCESS/ERROR MESSAGE DISPLAY (Crucial for testing login/logout feedback) -->
        @if (session('success'))
            <div class="bg-green-600 text-white p-4 rounded-lg mb-6 shadow-lg font-semibold" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-600 text-white p-4 rounded-lg mb-6 shadow-lg font-semibold" role="alert">
                <p>An error occurred:</p>
                <ul class="list-disc list-inside mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="mb-10">
            <h2 class="text-3xl font-semibold text-gray-200 mb-6 border-b border-gray-700 pb-2">Feature Navigation</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Meal Log Entry -->
                <a href="{{ route('meal_logs.index') }}" class="block p-6 bg-indigo-600 hover:bg-indigo-700 transition duration-300 rounded-lg shadow-lg transform hover:scale-[1.02]">
                    <div class="flex items-center space-x-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        <div>
                            <p class="text-xl font-bold text-white">View Meal Logs</p>
                            <p class="text-sm text-indigo-200">Manage your daily meal diaries.</p>
                        </div>
                    </div>
                </a>

                <!-- Placeholder: Recipe Management Entry -->
                <div class="block p-6 bg-gray-700 border border-gray-600 rounded-lg shadow-lg">
                    <div class="flex items-center space-x-4">
                        <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.657 0 3 .895 3 2s-1.343 2-3 2h-1a2 2 0 00-2 2v1m-4 5h8a2 2 0 002-2v-3a2 2 0 00-2-2H8a2 2 0 00-2 2v3a2 2 0 002 2z"></path></svg>
                        <div>
                            <p class="text-xl font-bold text-gray-300">Recipe Management</p>
                            <p class="text-sm text-gray-400">(Coming Soon)</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- LOGOUT FORM (For Auth Testing) -->
        <section class="mt-8">
            <h2 class="text-2xl font-semibold text-gray-200 mb-4 border-b border-gray-700 pb-2">Account Actions</h2>
            <form method="POST" action="{{ route('logout') }}">
                @csrf 
                <button type="submit" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg shadow-md transition duration-300 transform hover:scale-[1.01] hover:shadow-xl">
                    Log Out
                </button>
            </form>
        </section>

        <footer class="text-center pt-8 mt-10 border-t border-gray-700">
            <p class="text-sm text-gray-500">
                Current User ID: <span class="font-mono text-gray-300">{{ auth()->id() ?? 'Guest' }}</span>
            </p>
        </footer>
    </div>
</body>
</html>