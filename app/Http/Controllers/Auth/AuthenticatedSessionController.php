<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\GameMatch;
use App\Models\MatchInvitation;
use App\Models\PlayerProfile;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentApplication;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Map of demo accounts curated for evaluation and project demonstration.
     */
    protected array $demoAccounts = [
        'manager' => [
            'email' => 'prof.manager@leaguehub.com',
            'name' => '  Demo (Manager)',
        ],
        'player' => [
            'email' => 'prof.player@leaguehub.com',
            'name' => '  Demo (Player)',
        ],
        'viewer' => [
            'email' => 'prof.viewer@leaguehub.com',
            'name' => '  Demo (Viewer)',
        ],
    ];

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $email = Str::lower(trim($request->string('email')->toString()));
        $isDemoEmail = in_array($email, array_column($this->demoAccounts, 'email'), true);

        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => [$isDemoEmail ? 'nullable' : 'required', 'string'],
            'role' => ['nullable', 'string', 'in:manager,player,viewer'],
        ]);

        $user = User::where('email', $email)->first();

        // If this is a demo account and doesn't exist yet, provision it on demand
        if (! $user && $isDemoEmail) {
            $roleKey = array_search($email, array_column($this->demoAccounts, 'email', 'role'), true)
                ?: match ($email) {
                    'prof.manager@leaguehub.com' => 'manager',
                    'prof.player@leaguehub.com' => 'player',
                    default => 'viewer',
                };
            $user = $this->provisionDemoUser($roleKey);
        }

        if (! $user) {
            return back()->withErrors([
                'email' => 'No account was found with this email address.',
            ])->onlyInput('email');
        }

        // Demo accounts bypass password check for quick demonstration
        if (! $isDemoEmail && ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'password' => 'The password you entered is incorrect.',
            ])->onlyInput('email');
        }

        if ($user->account_status === 'suspended') {
            if ($isDemoEmail) {
                // Auto-restore demo accounts to avoid presentation interruptions
                $user->update([
                    'account_status' => 'active',
                    'suspension_matches_remaining' => 0,
                    'suspension_reason' => null,
                    'suspended_at' => null,
                    'suspended_by' => null,
                ]);
            } else {
                $matchesRemaining = (int) $user->suspension_matches_remaining;
                $reason = $user->suspension_reason
                    ? ' Reason: '.$user->suspension_reason
                    : '';

                return back()->withErrors([
                    'password' => "This account is suspended for {$matchesRemaining} remaining match(es).{$reason}",
                ])->onlyInput('email');
            }
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

    /**
     * Passwordless single-click demo login for academic evaluation.
     */
    public function storeDemo(Request $request): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'in:manager,player,viewer'],
        ]);

        $role = $request->string('role')->toString();
        $config = $this->demoAccounts[$role] ?? null;

        if (! $config) {
            return back()->withErrors(['role' => 'Invalid demo role selected.']);
        }

        $user = User::where('email', $config['email'])->first();

        if (! $user) {
            $user = $this->provisionDemoUser($role);
        }

        // Keep demo account active
        if ($user->account_status === 'suspended') {
            $user->update([
                'account_status' => 'active',
                'suspension_matches_remaining' => 0,
                'suspension_reason' => null,
                'suspended_at' => null,
                'suspended_by' => null,
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        $roleLabel = ucfirst($user->role);

        return redirect()->intended(route('mytournaments'))
            ->with('success', "Signed in as {$user->name} ({$roleLabel}) —   presentation access.");
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

    /**
     * Provision a rich demo user and matching ecosystem data if not present.
     */
    protected function provisionDemoUser(string $role): User
    {
        $password = Hash::make('demo12345');

        if ($role === 'manager') {
            $user = User::updateOrCreate(
                ['email' => 'prof.manager@leaguehub.com'],
                [
                    'name' => '  Demo (Manager)',
                    'password' => $password,
                    'role' => 'manager',
                    'areal_zone' => 'Metro Central',
                    'account_status' => 'active',
                ]
            );

            $team = Team::where('manager_id', $user->id)->first();
            if (! $team) {
                $team = Team::firstOrCreate(
                    ['slug' => ' -titans-fc'],
                    [
                        'manager_id' => $user->id,
                        'name' => '  Titans FC',
                        'sport_type' => 'Football',
                        'areal_ranking' => 1050,
                        'home_venue' => 'Grand Academic Stadium',
                        'description' => 'Showcase squad curated for comprehensive academic and system evaluation.',
                    ]
                );

                // Attach sample players to this squad
                $players = User::where('role', 'player')->where('id', '!=', $user->id)->take(6)->get();
                $positions = ['Forward', 'Midfielder', 'Defender', 'Goalkeeper', 'Winger', 'Center Back'];
                foreach ($players as $idx => $p) {
                    $team->players()->syncWithoutDetaching([
                        $p->id => [
                            'position' => $positions[$idx % count($positions)],
                            'status' => 'active',
                        ],
                    ]);
                }
            }

            $tournament = Tournament::first();
            if ($tournament && $team) {
                TournamentApplication::firstOrCreate(
                    [
                        'tournament_id' => $tournament->id,
                        'team_id' => $team->id,
                    ],
                    [
                        'status' => 'approved',
                        'notes' => 'Official showcase squad registration approved for tournament demonstration.',
                    ]
                );
            }

            return $user;
        }

        if ($role === 'player') {
            $user = User::updateOrCreate(
                ['email' => 'prof.player@leaguehub.com'],
                [
                    'name' => '  Demo (Player)',
                    'password' => $password,
                    'role' => 'player',
                    'areal_zone' => 'Metro Central',
                    'account_status' => 'active',
                ]
            );

            PlayerProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'jersey_number' => '10',
                    'primary_sport' => 'Football',
                    'is_injured' => false,
                    'injury_notes' => null,
                    'areal_ranking' => 10,
                    'career_stats' => [
                        'matches_played' => 18,
                        'goals_or_points' => 16,
                        'assists' => 10,
                        'mvp_count' => 5,
                        'form_rating' => 8.8,
                        'recent_ratings' => [7.9, 8.2, 8.6, 9.0, 8.5, 8.9, 8.8],
                    ],
                ]
            );

            $team = Team::where('slug', ' -titans-fc')->first() ?? Team::first();
            if ($team) {
                $team->players()->syncWithoutDetaching([
                    $user->id => [
                        'position' => 'Starting Playmaker',
                        'status' => 'active',
                    ],
                ]);

                $match = GameMatch::where('status', 'scheduled')->first() ?? GameMatch::first();
                if ($match) {
                    MatchInvitation::firstOrCreate(
                        [
                            'match_id' => $match->id,
                            'player_id' => $user->id,
                        ],
                        [
                            'team_id' => $team->id,
                            'status' => 'pending',
                            'manager_notes' => 'Tactical playmaker call-up for upcoming tournament showcase match.',
                        ]
                    );
                }
            }

            return $user;
        }

        // Viewer
        $user = User::updateOrCreate(
            ['email' => 'prof.viewer@leaguehub.com'],
            [
                'name' => '  Demo (Viewer)',
                'password' => $password,
                'role' => 'viewer',
                'areal_zone' => 'Metro Central',
                'account_status' => 'active',
            ]
        );

        $match = GameMatch::first();
        if ($match) {
            Bookmark::firstOrCreate([
                'user_id' => $user->id,
                'game_match_id' => $match->id,
            ]);
        }

        $tournament = Tournament::first();
        if ($tournament) {
            Bookmark::firstOrCreate([
                'user_id' => $user->id,
                'tournament_id' => $tournament->id,
            ]);
        }

        return $user;
    }
}
