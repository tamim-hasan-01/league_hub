<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
            'role' => ['nullable', 'string', 'in:manager,player,viewer'],
        ]);

        $email = Str::lower(trim($credentials['email']));
        $user = User::where('email', $email)->first();

        if (! $user) {
            return back()->withErrors([
                'email' => 'No account was found with this email address.',
            ])->onlyInput('email');
        }

        if (! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'password' => 'The password you entered is incorrect.',
            ])->onlyInput('email');
        }

        if ($user->account_status === 'suspended') {
            $matchesRemaining = (int) $user->suspension_matches_remaining;
            $reason = $user->suspension_reason
                ? ' Reason: '.$user->suspension_reason
                : '';

            return back()->withErrors([
                'password' => "This account is suspended for {$matchesRemaining} remaining match(es).{$reason}",
            ])->onlyInput('email');
        }

        $selectedRole = $request->string('role')->toString();
        if ($selectedRole !== '' && $user->role !== $selectedRole) {
            return back()->withErrors([
                'role' => "This account is registered as {$user->role}, not {$selectedRole}.",
            ])->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $roleLabel = ucfirst($user->role);
        $redirect = $user->isAdmin()
            ? route('admin.dashboard')
            : route('mytournaments');

        return redirect()->intended($redirect)
            ->with('success', "Welcome back, {$user->name}. Signed in as {$roleLabel}.");
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
            $names = [
                'sami' => 'Waliur Rafiq Sami',
                'siam' => 'Md. Tamim Hasan Siam',
                'mobahir' => 'Muhammad Mobasher Ahmmed Milon',
            ];

            $user = User::updateOrCreate(
                ['email' => $allowedAdmin['email']],
                [
                    'name' => $names[$adminId],
                    'password' => Hash::make($allowedAdmin['password']),
                    'role' => 'admin',
                    'areal_zone' => 'Metro Central',
                ]
            );
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
}
