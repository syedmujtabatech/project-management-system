<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            if (!Auth::user()->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account is deactivated. Please contact the administrator.']);
            }

            ActivityLog::log('User Login', Auth::user()->name . ' logged into the workspace.');
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function quickLogin(Request $request, string $role)
    {
        $user = match ($role) {
            'admin' => User::where('role', 'super_admin')->first(),
            'pm' => User::where('role', 'project_manager')->first(),
            'dev' => User::where('email', 'david.dev@apexcompany.com')->first() ?? User::where('role', 'team_member')->first(),
            'client' => User::where('role', 'client')->first(),
            default => null,
        };

        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();
            ActivityLog::log('Quick Demo Login', $user->name . " logged in as [{$user->role}].");
            return redirect()->route('dashboard')->with('success', "Welcome back, {$user->name}!");
        }

        return redirect()->route('login')->with('error', 'Demo user not found.');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            ActivityLog::log('User Logout', Auth::user()->name . ' logged out.');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->phone = $validated['phone'];
        $user->job_title = $validated['job_title'];
        $user->department = $validated['department'];

        if (!empty($validated['new_password'])) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Current password does not match.']);
            }
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();
        ActivityLog::log('Profile Updated', "{$user->name} updated their profile settings.");

        return back()->with('success', 'Profile updated successfully!');
    }
}
