<?php

namespace App\Services;

use App\Models\GameMatch;
use App\Models\Tournament;
use Carbon\Carbon;

class TournamentBracketService
{
    /**
     * Generate initial knockout bracket fixtures for approved teams.
     */
    public function generateBracket(Tournament $tournament): array
    {
        $teams = $tournament->teams()->get();
        if ($teams->count() < 2) {
            return ['success' => false, 'message' => 'At least 2 approved teams are required to generate bracket fixtures.'];
        }

        // Clear existing non-completed matches if regenerating
        $tournament->matches()->where('status', 'upcoming')->delete();

        $shuffled = $teams->shuffle()->values();
        $count = $shuffled->count();

        // Determine starting stage
        $stage = 'final';
        if ($count >= 8) {
            $stage = 'quarter_final';
            $numMatches = 4;
        } elseif ($count >= 4) {
            $stage = 'semi_final';
            $numMatches = 2;
        } else {
            $stage = 'final';
            $numMatches = 1;
        }

        $tournament->update([
            'status' => 'in_progress',
            'current_stage' => $stage,
        ]);

        $createdMatches = [];
        $baseDate = $tournament->start_date ? Carbon::parse($tournament->start_date) : Carbon::now()->addDays(2);

        for ($i = 0; $i < $numMatches; $i++) {
            $homeIndex = $i * 2;
            $awayIndex = $i * 2 + 1;

            $homeTeam = $shuffled[$homeIndex] ?? $shuffled[0];
            $awayTeam = $shuffled[$awayIndex] ?? $shuffled[1];

            $match = GameMatch::create([
                'tournament_id' => $tournament->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'stage' => $stage,
                'scheduled_at' => (clone $baseDate)->addHours(2 * $i + 14),
                'venue' => $tournament->venue ?? 'Main Championship Arena',
                'status' => 'upcoming',
                'home_score' => 0,
                'away_score' => 0,
                'match_data' => [
                    'bracket_match_number' => $i + 1,
                    'events' => [],
                    'periods' => ['P1' => '0-0', 'P2' => '0-0']
                ],
            ]);

            $createdMatches[] = $match;
        }

        return ['success' => true, 'matches' => $createdMatches];
    }

    /**
     * Advance tournament bracket to the next round if current round is completed.
     */
    public function advanceBracketIfCompleted(Tournament $tournament): bool
    {
        $currentStage = $tournament->current_stage;
        $matches = $tournament->matches()->where('stage', $currentStage)->get();

        if ($matches->isEmpty() || $matches->contains(fn($m) => $m->status !== 'completed')) {
            return false;
        }

        $winners = [];
        foreach ($matches as $match) {
            if ($match->home_score >= $match->away_score) {
                $winners[] = $match->home_team_id;
            } else {
                $winners[] = $match->away_team_id;
            }
        }

        if ($currentStage === 'quarter_final') {
            $nextStage = 'semi_final';
            $tournament->update(['current_stage' => $nextStage]);

            $date = Carbon::now()->addDays(3);
            for ($i = 0; $i < count($winners); $i += 2) {
                if (isset($winners[$i + 1])) {
                    GameMatch::create([
                        'tournament_id' => $tournament->id,
                        'home_team_id' => $winners[$i],
                        'away_team_id' => $winners[$i + 1],
                        'stage' => $nextStage,
                        'scheduled_at' => (clone $date)->addHours(3 * ($i / 2) + 16),
                        'venue' => $tournament->venue ?? 'Championship Stadium',
                        'status' => 'upcoming',
                        'home_score' => 0,
                        'away_score' => 0,
                        'match_data' => ['events' => []],
                    ]);
                }
            }
            return true;
        } elseif ($currentStage === 'semi_final') {
            $nextStage = 'final';
            $tournament->update(['current_stage' => $nextStage]);

            $date = Carbon::now()->addDays(5);
            if (count($winners) >= 2) {
                GameMatch::create([
                    'tournament_id' => $tournament->id,
                    'home_team_id' => $winners[0],
                    'away_team_id' => $winners[1],
                    'stage' => $nextStage,
                    'scheduled_at' => (clone $date)->setTime(19, 0),
                    'venue' => $tournament->venue ?? 'Grand Final Arena',
                    'status' => 'upcoming',
                    'home_score' => 0,
                    'away_score' => 0,
                    'match_data' => ['events' => []],
                ]);
            }
            return true;
        } elseif ($currentStage === 'final') {
            $tournament->update(['status' => 'completed']);
            return true;
        }

        return false;
    }

    /**
     * Get structured bracket structure for visual display.
     */
    public function getBracketTree(Tournament $tournament): array
    {
        $stages = ['quarter_final', 'semi_final', 'final'];
        $tree = [];

        foreach ($stages as $stage) {
            $matches = $tournament->matches()
                ->with(['homeTeam', 'awayTeam'])
                ->where('stage', $stage)
                ->orderBy('id')
                ->get();

            $tree[$stage] = $matches->map(function ($m) {
                return [
                    'id' => $m->id,
                    'stage' => $m->stage,
                    'status' => $m->status,
                    'home_team' => $m->homeTeam ? [
                        'id' => $m->homeTeam->id,
                        'name' => $m->homeTeam->name,
                        'logo' => $m->homeTeam->logo,
                        'score' => $m->home_score,
                    ] : null,
                    'away_team' => $m->awayTeam ? [
                        'id' => $m->awayTeam->id,
                        'name' => $m->awayTeam->name,
                        'logo' => $m->awayTeam->logo,
                        'score' => $m->away_score,
                    ] : null,
                    'scheduled_at' => $m->scheduled_at->format('M d, H:i'),
                    'venue' => $m->venue,
                    'winner_id' => $m->status === 'completed'
                        ? ($m->home_score >= $m->away_score ? $m->home_team_id : $m->away_team_id)
                        : null,
                ];
            });
        }

        return $tree;
    }
}

