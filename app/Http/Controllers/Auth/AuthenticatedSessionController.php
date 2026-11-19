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
                ->with('success', 'Logged in successfully as '.Auth::user()->name);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function storeAdmin(Request $request): RedirectResponse
    {
        $request->validate([
            'admin_id' => ['required', 'string', 'in:sami,siam,mobahir'],
            'password' => ['required', 'string'],
        ]);

        $admins = [
            'sami' => [
                'email' => 'sami@leaguehub.com',
                'password' => '123123123',
            ],
            'siam' => [
                'email' => 'siam@leaguehub.com',
                'password' => '1234512345',
            ],
            'mobahir' => [
                'email' => 'mobahir@leaguehub.com',
                'password' => '123456789',
            ],
        ];

        $adminId = $request->string('admin_id')->toString();
        $allowedAdmin = $admins[$adminId];

        if (! hash_equals($allowedAdmin['password'], $request->string('password')->toString())) {
            return back()->withErrors([
                'password' => 'The selected administrator password is incorrect.',
            ])->onlyInput();
        }

        $user = User::where('email', $allowedAdmin['email'])
            ->where('role', 'admin')
            ->first();

        if (! $user) {
            return back()->withErrors([
                'password' => 'This administrator account is not available. Run the database seeder.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', 'Verified administrator access for '.$user->name.'.');
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
        if (! in_array($role, $allowed)) {
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
