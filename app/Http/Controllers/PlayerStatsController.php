<?php

namespace App\Http\Controllers;

use App\Models\PlayerProfile;
use App\Models\MatchInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PlayerStatsController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Ensure user has player profile
        $profile = $user->playerProfile;
        if (!$profile) {
            $profile = PlayerProfile::create([
                'user_id' => $user->id,
                'jersey_number' => '10',
                'primary_sport' => 'Football',
                'is_injured' => false,
                'injury_notes' => null,
                'areal_ranking' => 42,
                'career_stats' => [
                    'matches_played' => 14,
                    'goals_or_points' => 11,
                    'assists' => 7,
                    'mvp_count' => 3,
                    'form_rating' => 8.1,
                    'recent_ratings' => [7.5, 7.8, 8.2, 8.5, 7.9, 8.4, 8.1],
                ],
            ]);
        }

        // Teams user belongs to
        $teams = $user->teams()->with('manager')->get();

        // Match invitations history
        $invitations = MatchInvitation::with(['match.homeTeam', 'match.awayTeam', 'match.tournament', 'team'])
            ->where('player_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $chartLabels = ['Match 1', 'Match 2', 'Match 3', 'Match 4', 'Match 5', 'Match 6', 'Match 7'];
        $chartRatings = $profile->career_stats['recent_ratings'] ?? [7.0, 7.2, 7.5, 7.8, 8.1, 7.9, 8.2];

        $radarData = [
            'pace' => 84,
            'technique' => 88,
            'stamina' => 80,
            'teamwork' => 92,
            'defense' => 74,
            'mental' => 86,
        ];

        return view('pages.player.stats', compact(
            'profile',
            'teams',
            'invitations',
            'chartLabels',
            'chartRatings',
            'radarData'
        ));
    }

    public function toggleHealth(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->playerProfile;

        if (!$profile) {
            return back()->with('error', 'Player profile not found.');
        }

        $request->validate([
            'is_injured' => 'required|boolean',
            'injury_notes' => 'nullable|string|max:500',
        ]);

        $profile->update([
            'is_injured' => $request->boolean('is_injured'),
            'injury_notes' => $request->boolean('is_injured') ? $request->injury_notes : null,
        ]);

        $statusText = $profile->is_injured ? 'Injured (Medical hold active)' : 'Active (Available for selection)';
        return back()->with('success', "Health status updated to: {$statusText}");
    }
}

