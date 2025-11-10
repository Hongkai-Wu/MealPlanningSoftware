<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Meal Planning Software</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
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
        <h2 class="text-4xl font-bold text-center text-indigo-400 mb-6">Create Account</h2>
        <p class="text-center text-gray-400 mb-8">Start tracking your healthy meals today!</p>

        <!-- ERROR MESSAGE DISPLAY -->
        @if ($errors->any())
            <div class="bg-red-600 text-white p-4 rounded-lg mb-4 shadow-md font-semibold" role="alert">
                <p>Registration failed:</p>
                <ul class="list-disc list-inside mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <!-- Name Field -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus 
                       class="w-full p-3 border border-gray-600 bg-gray-700 rounded-lg text-white focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-inner">
            </div>

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required 
                       class="w-full p-3 border border-gray-600 bg-gray-700 rounded-lg text-white focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-inner">
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" 
                       class="w-full p-3 border border-gray-600 bg-gray-700 rounded-lg text-white focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-inner">
            </div>

            <!-- Password Confirmation Field -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" 
                       class="w-full p-3 border border-gray-600 bg-gray-700 rounded-lg text-white focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-inner">
            </div>

            <!-- Register Button -->
            <div>
                <button type="submit" 
                        class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-xl transition duration-300 transform hover:scale-[1.01]">
                    Register
                </button>
            </div>
        </form>

        <!-- Link to Login -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-400">
                Already have an account? 
                <a href="{{ route('login') }}" class="font-medium text-indigo-400 hover:text-indigo-300 transition duration-150">
                    Log In
                </a>
            </p>
        </div>
    </div>
</body>
</html>