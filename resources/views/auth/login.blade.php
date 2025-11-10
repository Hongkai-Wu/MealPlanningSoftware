<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Meal Planning Software</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
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
    <div class="w-full max-w-md p-8 bg-gray-800 rounded-xl shadow-2xl">
        <h2 class="text-4xl font-bold text-center text-indigo-400 mb-6">Welcome Back</h2>
        <p class="text-center text-gray-400 mb-8">Sign in to access your meal plans.</p>

        <!-- ERROR MESSAGE DISPLAY -->
        @if ($errors->any())
            <div class="bg-red-600 text-white p-4 rounded-lg mb-4 shadow-md font-semibold" role="alert">
                <p>Login failed. Please check your credentials:</p>
                <ul class="list-disc list-inside mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                       class="w-full p-3 border border-gray-600 bg-gray-700 rounded-lg text-white focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-inner">
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" 
                       class="w-full p-3 border border-gray-600 bg-gray-700 rounded-lg text-white focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-inner">
            </div>

            <!-- Remember Me Checkbox (Optional) -->
            <div class="flex items-center">
                <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 text-indigo-600 border-gray-600 rounded focus:ring-indigo-500 bg-gray-700">
                <label for="remember_me" class="ml-2 block text-sm text-gray-400">Remember me</label>
            </div>

            <!-- Login Button -->
            <div>
                <button type="submit" 
                        class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-xl transition duration-300 transform hover:scale-[1.01]">
                    Log In
                </button>
            </div>
        </form>

        <!-- Link to Registration Page -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-400">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-medium text-indigo-400 hover:text-indigo-300 transition duration-150">
                    Register here
                </a>
            </p>
        </div>
    </div>
</body>
</html>