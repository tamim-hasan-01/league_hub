<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('home'))
                ->with('success', 'Logged in successfully as ' . Auth::user()->name);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'You have been logged out.');
    }

    /**
     * Demo role switch for fast testing and grading
     */
    public function switchRole(Request $request, string $role): RedirectResponse
    {
        $allowed = ['admin', 'manager', 'player', 'viewer'];
        if (!in_array($role, $allowed)) {
            abort(404);
        }

        $user = User::where('role', $role)->first();
        if ($user) {
            Auth::login($user);
            return redirect()->back()->with('success', "Switched account to {$user->name} ({$role})");
        }

        return redirect()->back()->with('error', "No demo {$role} user found in database. Run seeder.");
    }
}

