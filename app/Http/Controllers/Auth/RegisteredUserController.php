<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PlayerProfile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:manager,player,viewer'],
            'areal_zone' => ['nullable', 'string', 'max:100'],
            'primary_sport' => ['nullable', 'string', 'max:50'],
            'jersey_number' => ['nullable', 'integer', 'between:1,99'],
        ]);

        $user = User::create([
            'name' => Str::squish($request->string('name')->toString()),
            'email' => Str::lower(trim($request->string('email')->toString())),
            'password' => Hash::make($request->string('password')->toString()),
            'role' => $request->string('role')->toString(),
            'areal_zone' => $request->filled('areal_zone')
                ? $request->string('areal_zone')->toString()
                : 'Metro Central',
        ]);

        if ($user->role === 'player') {
            PlayerProfile::create([
                'user_id' => $user->id,
                'jersey_number' => $request->filled('jersey_number')
                    ? (string) $request->integer('jersey_number')
                    : (string) rand(1, 99),
                'primary_sport' => $request->filled('primary_sport')
                    ? $request->string('primary_sport')->toString()
                    : 'Football',
                'is_injured' => false,
                'injury_notes' => null,
                'areal_ranking' => rand(15, 80),
                'career_stats' => [
                    'matches_played' => 0,
                    'goals_or_points' => 0,
                    'assists' => 0,
                    'mvp_count' => 0,
                    'form_rating' => 7.5,
                    'recent_ratings' => [7.5],
                ],
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Registration successful! Welcome to the League Platform.');
    }
}
