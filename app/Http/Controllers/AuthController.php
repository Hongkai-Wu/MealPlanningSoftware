<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Import the Hash facade for password hashing/checking
use Illuminate\Support\Facades\Hash;
// Import the Auth facade for user authentication/logout
use Illuminate\Support\Facades\Auth;
// Assume you have a User model
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the registration form.
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        // Returns the registration view (resources/views/auth/register.blade.php)
        return view('auth.register');
    }

    /**
     * Handle the incoming user registration request.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // 1. Validate user input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // Ensure email is unique
            'password' => 'required|string|min:8|confirmed', // 'confirmed' requires a password_confirmation field
        ]);

        // 2. Create the new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash the password before storing
        ]);

        // 3. Log the new user in (Optional, but common practice)
        Auth::login($user);

        // 4. Redirect to the dashboard with a success message
        return redirect('/dashboard')->with('success', 'Registration successful! You are now logged in.');
    }

    /**
     * Show the login form.
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // Returns the login view (resources/views/auth/login.blade.php)
        return view('auth.login');
    }

    /**
     * Handle the incoming user login request.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // 1. Validate user input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // 2. Attempt to authenticate the user
        $remember = $request->boolean('remember'); // Get the 'remember me' option

        if (Auth::attempt($credentials, $remember)) {
            // Authentication successful
            $request->session()->regenerate(); // Regenerate the session ID to prevent session fixation

            return redirect()->intended('/dashboard')->with('success', 'Login successful!');
        }

        // Authentication failed: redirect back with error message
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle the user logout request.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout(); // Log out the current user

        $request->session()->invalidate(); // Invalidate the current session
        $request->session()->regenerateToken(); // Regenerate the CSRF token

        // Redirect to the home or login page
        return redirect('/')->with('success', 'You have been logged out.');
    }
}