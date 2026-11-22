<?php

namespace Database\Seeders;

use App\Models\Bookmark;
use App\Models\GameMatch;
use App\Models\MatchInvitation;
use App\Models\NewsArticle;
use App\Models\PlayerProfile;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentApplication;
use App\Models\User;
use App\Notifications\MatchInvitationReceived;
use App\Notifications\MatchResultNotification;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with realistic sports ecosystem data.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Core Users with distinct roles
        $admin = User::create([
            'name' => 'Waliur Rafiq Sami',
            'email' => 'sami@leaguehub.com',
            'password' => Hash::make('123123123'),
            'role' => 'admin',
            'areal_zone' => 'Metro Central',
        ]);

        User::create([
            'name' => 'Md. Tamim Hasan Siam',
            'email' => 'siam@leaguehub.com',
            'password' => Hash::make('1234512345'),
            'role' => 'admin',
            'areal_zone' => 'Metro Central',
        ]);

        User::create([
            'name' => 'Muhammad Mobasher Ahmmed Milon',
            'email' => 'mobahir@leaguehub.com',
            'password' => Hash::make('123456789'),
            'role' => 'admin',
            'areal_zone' => 'Metro Central',
        ]);

        $manager1 = User::create([
            'name' => 'Coach Elena Rostova',
            'email' => 'manager@leaguehub.com',
            'password' => $password,
            'role' => 'manager',
            'areal_zone' => 'Pacific Coast',
        ]);

        $manager2 = User::create([
            'name' => 'Dave "Strats" Miller',
            'email' => 'manager2@leaguehub.com',
            'password' => $password,
            'role' => 'manager',
            'areal_zone' => 'Eastern Summit',
        ]);

        $manager3 = User::create([
            'name' => 'Carlos Santana',
            'email' => 'manager3@leaguehub.com',
            'password' => $password,
            'role' => 'manager',
            'areal_zone' => 'North Region',
        ]);

        $player1 = User::create([
            'name' => 'Kai "Phantom" Sterling',
            'email' => 'player@leaguehub.com',
            'password' => $password,
            'role' => 'player',
            'areal_zone' => 'Metro Central',
        ]);

        $playerInjured = User::create([
            'name' => 'Lucas "Sniper" Thorne',
            'email' => 'injured@leaguehub.com',
            'password' => $password,
            'role' => 'player',
            'areal_zone' => 'Pacific Coast',
        ]);

        $viewer = User::create([
            'name' => 'Sarah Jenkins',
            'email' => 'viewer@leaguehub.com',
            'password' => $password,
            'role' => 'viewer',
            'areal_zone' => 'Metro Central',
        ]);

        //   Demo Accounts (Passwordless role accounts for academic presentation)
        $profManager = User::create([
            'name' => '  Demo (Manager)',
            'email' => 'prof.manager@leaguehub.com',
            'password' => $password,
            'role' => 'manager',
            'areal_zone' => 'Metro Central',
        ]);

        $profPlayer = User::create([
            'name' => '  Demo (Player)',
            'email' => 'prof.player@leaguehub.com',
            'password' => $password,
            'role' => 'player',
            'areal_zone' => 'Metro Central',
        ]);

        $profViewer = User::create([
            'name' => '  Demo (Viewer)',
            'email' => 'prof.viewer@leaguehub.com',
            'password' => $password,
            'role' => 'viewer',
            'areal_zone' => 'Metro Central',
        ]);

        // Create player profiles for main players
        PlayerProfile::create([
            'user_id' => $player1->id,
            'jersey_number' => '7',
            'primary_sport' => 'Football',
            'is_injured' => false,
            'injury_notes' => null,
            'areal_ranking' => 12,
            'career_stats' => [
                'matches_played' => 18,
                'goals_or_points' => 15,
                'assists' => 9,
                'mvp_count' => 4,
                'form_rating' => 8.4,
                'recent_ratings' => [7.6, 8.0, 8.2, 8.8, 7.9, 8.5, 8.4],
            ],
        ]);

        PlayerProfile::create([
            'user_id' => $playerInjured->id,
            'jersey_number' => '10',
            'primary_sport' => 'Football',
            'is_injured' => true,
            'injury_notes' => 'Grade 2 hamstring strain sustained during semifinal match. Medically grounded for 14 days.',
            'areal_ranking' => 28,
            'career_stats' => [
                'matches_played' => 12,
                'goals_or_points' => 8,
                'assists' => 4,
                'mvp_count' => 2,
                'form_rating' => 7.8,
                'recent_ratings' => [7.2, 7.5, 8.0, 7.9],
            ],
        ]);

        PlayerProfile::create([
            'user_id' => $profPlayer->id,
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
        ]);

        // Generate 12 more diverse players
        $playerNames = [
            ['name' => 'Darius "Viper" Cole', 'sport' => 'Football', 'jersey' => '9', 'injured' => false, 'notes' => null],
            ['name' => 'Maya "Blitz" Lin', 'sport' => 'Football', 'jersey' => '4', 'injured' => false, 'notes' => null],
            ['name' => 'Jaxson Reed', 'sport' => 'Football', 'jersey' => '1', 'injured' => false, 'notes' => null],
            ['name' => 'Tate "Aero" Walker', 'sport' => 'Esports', 'jersey' => '11', 'injured' => false, 'notes' => null],
            ['name' => 'Chloe Vance', 'sport' => 'Esports', 'jersey' => '23', 'injured' => true, 'notes' => 'Repetitive wrist strain / Carpal tunnel rest order by physio.'],
            ['name' => 'Rico "Bulldozer" Morales', 'sport' => 'Basketball', 'jersey' => '24', 'injured' => false, 'notes' => null],
            ['name' => 'Liam Chen', 'sport' => 'Basketball', 'jersey' => '30', 'injured' => false, 'notes' => null],
            ['name' => 'Sanjay Patel', 'sport' => 'Cricket', 'jersey' => '18', 'injured' => false, 'notes' => null],
            ['name' => 'Aaron O\'Connor', 'sport' => 'Cricket', 'jersey' => '45', 'injured' => false, 'notes' => null],
            ['name' => 'Zane "Apex" Scott', 'sport' => 'Esports', 'jersey' => '99', 'injured' => false, 'notes' => null],
            ['name' => 'Noah "Shadow" Taylor', 'sport' => 'Football', 'jersey' => '8', 'injured' => false, 'notes' => null],
            ['name' => 'Leo Gallagher', 'sport' => 'Football', 'jersey' => '17', 'injured' => true, 'notes' => 'Ankle ligament sprain from match against Titan Vanguard.'],
        ];

        $extraPlayers = [];
        foreach ($playerNames as $idx => $p) {
            $user = User::create([
                'name' => $p['name'],
                'email' => 'athlete'.($idx + 1).'@leaguehub.com',
                'password' => $password,
                'role' => 'player',
                'areal_zone' => ['Metro Central', 'Pacific Coast', 'Eastern Summit', 'North Region'][$idx % 4],
            ]);

            PlayerProfile::create([
                'user_id' => $user->id,
                'jersey_number' => $p['jersey'],
                'primary_sport' => $p['sport'],
                'is_injured' => $p['injured'],
                'injury_notes' => $p['notes'],
                'areal_ranking' => rand(15, 95),
                'career_stats' => [
                    'matches_played' => rand(6, 22),
                    'goals_or_points' => rand(4, 28),
                    'assists' => rand(2, 14),
                    'mvp_count' => rand(0, 5),
                    'form_rating' => round(rand(70, 92) / 10, 1),
                    'recent_ratings' => [7.2, 7.5, 7.8, 8.1, 7.9],
                ],
            ]);

            $extraPlayers[] = $user;
        }

        // 2. Teams (across Football, Esports, Basketball, Cricket)
        $teamsData = [
            // Football Teams
            [
                'manager_id' => $manager1->id,
                'name' => 'Apex Sentinels FC',
                'slug' => 'apex-sentinels-fc',
                'sport_type' => 'Football',
                'areal_ranking' => 1120,
                'home_venue' => 'Sentinels Park Stadium',
                'description' => 'Reigning division champions with high-tempo pressing and clinical counter-attacks.',
            ],
            [
                'manager_id' => $manager2->id,
                'name' => 'Shadow Vipers United',
                'slug' => 'shadow-vipers-united',
                'sport_type' => 'Football',
                'areal_ranking' => 1085,
                'home_venue' => 'Viper Pit Arena',
                'description' => 'Fierce defensive unit famed for unbreakable mid-block and disciplined tactics.',
            ],
            [
                'manager_id' => $manager3->id,
                'name' => 'Titan Vanguard',
                'slug' => 'titan-vanguard',
                'sport_type' => 'Football',
                'areal_ranking' => 1040,
                'home_venue' => 'Colossus Ground',
                'description' => 'Physical powerhouse featuring dominant aerial dominance on set pieces.',
            ],
            [
                'manager_id' => $manager1->id,
                'name' => 'Hyperion Strikers',
                'slug' => 'hyperion-strikers',
                'sport_type' => 'Football',
                'areal_ranking' => 1010,
                'home_venue' => 'Solaris Stadium',
                'description' => 'Dynamic youth academy turned competitive regional contender.',
            ],
            [
                'manager_id' => $manager2->id,
                'name' => 'Ironclad Warriors',
                'slug' => 'ironclad-warriors',
                'sport_type' => 'Football',
                'areal_ranking' => 990,
                'home_venue' => 'Fortress Arena',
                'description' => 'Tenacious contenders known for comeback victories under pressure.',
            ],
            [
                'manager_id' => $manager3->id,
                'name' => 'Zephyr Rovers',
                'slug' => 'zephyr-rovers',
                'sport_type' => 'Football',
                'areal_ranking' => 975,
                'home_venue' => 'Breeze Park',
                'description' => 'Fluid passing maestros playing total possession football.',
            ],
            [
                'manager_id' => $manager1->id,
                'name' => 'Onyx Rangers',
                'slug' => 'onyx-rangers',
                'sport_type' => 'Football',
                'areal_ranking' => 960,
                'home_venue' => 'Midnight Field',
                'description' => 'Fast transitions and lethal wing play.',
            ],
            [
                'manager_id' => $manager2->id,
                'name' => 'Solar Flare FC',
                'slug' => 'solar-flare-fc',
                'sport_type' => 'Football',
                'areal_ranking' => 950,
                'home_venue' => 'Sunlight Stadium',
                'description' => 'High-octane offensive unit averaging 3 goals per game.',
            ],

            // Esports Teams
            [
                'manager_id' => $manager1->id,
                'name' => 'CyberPulse Esports',
                'slug' => 'cyberpulse-esports',
                'sport_type' => 'Esports',
                'areal_ranking' => 1140,
                'home_venue' => 'Neon Grid Coliseum',
                'description' => 'Premier FPS powerhouse with unrivaled site execution and clutch win percentages.',
            ],
            [
                'manager_id' => $manager2->id,
                'name' => 'Nebula Strike Gaming',
                'slug' => 'nebula-strike-gaming',
                'sport_type' => 'Esports',
                'areal_ranking' => 1090,
                'home_venue' => 'Starlight LAN Center',
                'description' => 'Methodical tactical squad boasting deep map veto mastery.',
            ],

            // Basketball Teams
            [
                'manager_id' => $manager3->id,
                'name' => 'Pacific Hawks',
                'slug' => 'pacific-hawks',
                'sport_type' => 'Basketball',
                'areal_ranking' => 1070,
                'home_venue' => 'Bayview Pavilion',
                'description' => 'High-flying fast break unit leading the conference in fastbreak points.',
            ],
            [
                'manager_id' => $manager1->id,
                'name' => 'Metro Dynamos',
                'slug' => 'metro-dynamos',
                'sport_type' => 'Basketball',
                'areal_ranking' => 1030,
                'home_venue' => 'Downtown Arena',
                'description' => 'Elite rim protectors and 3-point perimeter specialists.',
            ],

            //   Showcase Team
            [
                'manager_id' => $profManager->id,
                'name' => '  Titans FC',
                'slug' => ' -titans-fc',
                'sport_type' => 'Football',
                'areal_ranking' => 1050,
                'home_venue' => 'Grand Academic Stadium',
                'description' => 'Flagship tactical squad managed for academic and strategic demonstration.',
            ],
        ];

        $createdTeams = [];
        foreach ($teamsData as $td) {
            $createdTeams[$td['slug']] = Team::create($td);
        }

        // Attach Players to Teams
        //   Showcase Team
        $createdTeams[' -titans-fc']->players()->attach($profPlayer->id, ['position' => 'Starting Playmaker', 'status' => 'active']);
        $createdTeams[' -titans-fc']->players()->attach($extraPlayers[7]->id, ['position' => 'Central Midfielder', 'status' => 'active']);
        $createdTeams[' -titans-fc']->players()->attach($extraPlayers[8]->id, ['position' => 'Key Defender', 'status' => 'active']);

        // Kai Sterling in Apex Sentinels FC
        $createdTeams['apex-sentinels-fc']->players()->attach($player1->id, ['position' => 'Starting Forward / Striker', 'status' => 'active']);
        $createdTeams['apex-sentinels-fc']->players()->attach($extraPlayers[0]->id, ['position' => 'Midfield Maestro', 'status' => 'active']);
        $createdTeams['apex-sentinels-fc']->players()->attach($extraPlayers[1]->id, ['position' => 'Key Defender', 'status' => 'active']);
        $createdTeams['apex-sentinels-fc']->players()->attach($extraPlayers[2]->id, ['position' => 'Goalkeeper', 'status' => 'active']);

        // Lucas Thorne (Injured) in Shadow Vipers
        $createdTeams['shadow-vipers-united']->players()->attach($playerInjured->id, ['position' => 'Playmaker (Injured)', 'status' => 'active']);
        $createdTeams['shadow-vipers-united']->players()->attach($extraPlayers[10]->id, ['position' => 'Central Midfielder', 'status' => 'active']);
        $createdTeams['shadow-vipers-united']->players()->attach($extraPlayers[11]->id, ['position' => 'Sweeper (Injured)', 'status' => 'active']);

        // Esports team
        $createdTeams['cyberpulse-esports']->players()->attach($extraPlayers[3]->id, ['position' => 'Duelist / Entry', 'status' => 'active']);
        $createdTeams['cyberpulse-esports']->players()->attach($extraPlayers[4]->id, ['position' => 'In-Game Leader', 'status' => 'active']);
        $createdTeams['cyberpulse-esports']->players()->attach($extraPlayers[9]->id, ['position' => 'Initiator', 'status' => 'active']);

        // Basketball team
        $createdTeams['pacific-hawks']->players()->attach($extraPlayers[5]->id, ['position' => 'Point Guard', 'status' => 'active']);
        $createdTeams['pacific-hawks']->players()->attach($extraPlayers[6]->id, ['position' => 'Center', 'status' => 'active']);

        // 3. Tournaments
        $tournaments = [
            'apex-world-cup' => Tournament::create([
                'creator_id' => $admin->id,
                'title' => 'Apex Continental Championship 2026',
                'slug' => 'apex-continental-championship-2026',
                'sport_type' => 'Football',
                'status' => 'in_progress',
                'current_stage' => 'quarter_final',
                'description' => 'The premier knockout football competition featuring the top 8 seeded regional franchises competing for the grand cup and gold medals.',
                'venue' => 'Metropolis Grand Olympic Stadium',
                'start_date' => Carbon::now()->subDays(3),
                'end_date' => Carbon::now()->addDays(12),
                'prize_pool' => '$100,000 USD',
                'max_teams' => 8,
            ]),
            'esports-masters' => Tournament::create([
                'creator_id' => $admin->id,
                'title' => 'Global Esports Masters Invitational',
                'slug' => 'global-esports-masters-invitational',
                'sport_type' => 'Esports',
                'status' => 'registration_open',
                'current_stage' => 'group',
                'description' => 'Elite FPS tournament featuring double-elimination brackets and international teams vying for world ranking supremacy.',
                'venue' => 'Neon Grid Coliseum (LAN Hub)',
                'start_date' => Carbon::now()->addDays(5),
                'end_date' => Carbon::now()->addDays(20),
                'prize_pool' => '$75,000 USD',
                'max_teams' => 8,
            ]),
            'pacific-hoops' => Tournament::create([
                'creator_id' => $admin->id,
                'title' => 'Pacific Metro Basketball Cup',
                'slug' => 'pacific-metro-basketball-cup',
                'sport_type' => 'Basketball',
                'status' => 'in_progress',
                'current_stage' => 'semi_final',
                'description' => 'Fast-paced conference showdown showcasing collegiate standouts and pro circuit basketball contenders.',
                'venue' => 'Bayview Sports Pavilion',
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(7),
                'prize_pool' => '$30,000 USD',
                'max_teams' => 4,
            ]),
            'premier-cricket' => Tournament::create([
                'creator_id' => $admin->id,
                'title' => 'Premier T20 Cricket Challenge',
                'slug' => 'premier-t20-cricket-challenge',
                'sport_type' => 'Cricket',
                'status' => 'completed',
                'current_stage' => 'final',
                'description' => 'High scoring twenty-twenty clash of titans with thrilling boundary action and explosive batting.',
                'venue' => 'Crown Cricket Oval',
                'start_date' => Carbon::now()->subDays(20),
                'end_date' => Carbon::now()->subDays(2),
                'prize_pool' => '$50,000 USD',
                'max_teams' => 4,
            ]),
        ];

        // 4. Enroll 8 Football Teams in Apex Continental Championship
        $footballTeamSlugs = [
            'apex-sentinels-fc', 'shadow-vipers-united', 'titan-vanguard', 'hyperion-strikers',
            'ironclad-warriors', 'zephyr-rovers', 'onyx-rangers', 'solar-flare-fc',
        ];

        foreach ($footballTeamSlugs as $slug) {
            TournamentApplication::create([
                'tournament_id' => $tournaments['apex-world-cup']->id,
                'team_id' => $createdTeams[$slug]->id,
                'status' => 'approved',
                'notes' => 'Roster verified, medical clearance verified by commissioner.',
            ]);
        }

        // Tournament application for   Demo Team
        TournamentApplication::create([
            'tournament_id' => $tournaments['apex-world-cup']->id,
            'team_id' => $createdTeams[' -titans-fc']->id,
            'status' => 'approved',
            'notes' => 'Official showcase squad registration approved for tournament demonstration.',
        ]);

        // Pending applications in Esports tournament
        TournamentApplication::create([
            'tournament_id' => $tournaments['esports-masters']->id,
            'team_id' => $createdTeams['cyberpulse-esports']->id,
            'status' => 'approved',
            'notes' => 'Invited top-seed franchise.',
        ]);

        TournamentApplication::create([
            'tournament_id' => $tournaments['esports-masters']->id,
            'team_id' => $createdTeams['nebula-strike-gaming']->id,
            'status' => 'pending',
            'notes' => 'Application submitted by Coach Dave Miller. Awaiting tournament director review.',
        ]);

        // 5. Matches (Live, Upcoming, Completed, Friendlies)

        // MATCH 1: LIVE NOW - Apex Sentinels vs Shadow Vipers (Quarterfinal 1)
        $liveMatch = GameMatch::create([
            'tournament_id' => $tournaments['apex-world-cup']->id,
            'home_team_id' => $createdTeams['apex-sentinels-fc']->id,
            'away_team_id' => $createdTeams['shadow-vipers-united']->id,
            'stage' => 'quarter_final',
            'scheduled_at' => Carbon::now()->subMinutes(65),
            'venue' => 'Metropolis Grand Olympic Stadium',
            'status' => 'live',
            'home_score' => 2,
            'away_score' => 1,
            'match_data' => [
                'current_minute' => 68,
                'period' => '2nd Half',
                'events' => [
                    ['time' => '14\'', 'type' => 'score', 'description' => 'GOAL! Kai Sterling chips over the goalkeeper from 18 yards.'],
                    ['time' => '39\'', 'type' => 'score', 'description' => 'GOAL! Shadow Vipers equalize on a thunderous header.'],
                    ['time' => '52\'', 'type' => 'score', 'description' => 'GOAL! Apex Sentinels regain lead on a quick counterattack.'],
                    ['time' => '61\'', 'type' => 'yellow_card', 'description' => 'Yellow card issued to Viper defender for tactical tug.'],
                ],
            ],
        ]);

        // MATCH 2: Upcoming Quarterfinal 2
        $qf2 = GameMatch::create([
            'tournament_id' => $tournaments['apex-world-cup']->id,
            'home_team_id' => $createdTeams['titan-vanguard']->id,
            'away_team_id' => $createdTeams['hyperion-strikers']->id,
            'stage' => 'quarter_final',
            'scheduled_at' => Carbon::now()->addHours(4),
            'venue' => 'Colossus Ground Arena',
            'status' => 'upcoming',
            'home_score' => 0,
            'away_score' => 0,
            'match_data' => ['events' => []],
        ]);

        // MATCH 3: Upcoming Quarterfinal 3
        $qf3 = GameMatch::create([
            'tournament_id' => $tournaments['apex-world-cup']->id,
            'home_team_id' => $createdTeams['ironclad-warriors']->id,
            'away_team_id' => $createdTeams['zephyr-rovers']->id,
            'stage' => 'quarter_final',
            'scheduled_at' => Carbon::now()->addDays(1)->setTime(18, 0),
            'venue' => 'Fortress Arena',
            'status' => 'upcoming',
            'home_score' => 0,
            'away_score' => 0,
            'match_data' => ['events' => []],
        ]);

        // MATCH 4: Upcoming Quarterfinal 4
        $qf4 = GameMatch::create([
            'tournament_id' => $tournaments['apex-world-cup']->id,
            'home_team_id' => $createdTeams['onyx-rangers']->id,
            'away_team_id' => $createdTeams['solar-flare-fc']->id,
            'stage' => 'quarter_final',
            'scheduled_at' => Carbon::now()->addDays(1)->setTime(20, 30),
            'venue' => 'Sunlight Stadium',
            'status' => 'upcoming',
            'home_score' => 0,
            'away_score' => 0,
            'match_data' => ['events' => []],
        ]);

        // MATCH 5: Completed Match (Friendly Exhibition)
        $compMatch = GameMatch::create([
            'tournament_id' => null, // Friendly
            'home_team_id' => $createdTeams['apex-sentinels-fc']->id,
            'away_team_id' => $createdTeams['titan-vanguard']->id,
            'stage' => 'friendly',
            'scheduled_at' => Carbon::now()->subDays(2)->setTime(19, 0),
            'venue' => 'Sentinels Park Stadium',
            'status' => 'completed',
            'home_score' => 3,
            'away_score' => 1,
            'match_data' => [
                'mvp' => 'Kai Sterling',
                'events' => [
                    ['time' => '22\'', 'type' => 'score', 'description' => 'Apex Sentinels strike first on volley.'],
                    ['time' => '44\'', 'type' => 'score', 'description' => 'Titan Vanguard level through corner kick header.'],
                    ['time' => '71\'', 'type' => 'score', 'description' => 'Apex Sentinels score penalty.'],
                    ['time' => '88\'', 'type' => 'score', 'description' => 'Apex Sentinels seal 3-1 victory.'],
                ],
            ],
        ]);

        // MATCH 6: Completed Match 2
        GameMatch::create([
            'tournament_id' => null,
            'home_team_id' => $createdTeams['shadow-vipers-united']->id,
            'away_team_id' => $createdTeams['hyperion-strikers']->id,
            'stage' => 'friendly',
            'scheduled_at' => Carbon::now()->subDays(4)->setTime(17, 30),
            'venue' => 'Viper Pit Arena',
            'status' => 'completed',
            'home_score' => 2,
            'away_score' => 0,
            'match_data' => [
                'events' => [
                    ['time' => '30\'', 'type' => 'score', 'description' => 'Vipers convert free-kick.'],
                    ['time' => '81\'', 'type' => 'score', 'description' => 'Late counter-strike finishes match.'],
                ],
            ],
        ]);

        // MATCH 7: Live Basketball Game
        GameMatch::create([
            'tournament_id' => $tournaments['pacific-hoops']->id,
            'home_team_id' => $createdTeams['pacific-hawks']->id,
            'away_team_id' => $createdTeams['metro-dynamos']->id,
            'stage' => 'semi_final',
            'scheduled_at' => Carbon::now()->subMinutes(30),
            'venue' => 'Bayview Sports Pavilion',
            'status' => 'live',
            'home_score' => 78,
            'away_score' => 74,
            'match_data' => [
                'quarter' => 'Q4 04:12',
                'events' => [
                    ['time' => 'Q1', 'type' => 'score', 'description' => 'Hawks lead 24-20'],
                    ['time' => 'Q2', 'type' => 'score', 'description' => 'Dynamos tie game 45-45 at half'],
                    ['time' => 'Q4', 'type' => 'score', 'description' => 'Deep 3-pointer by Rico Morales pushes Hawks ahead!'],
                ],
            ],
        ]);

        // 6. Match Invitations for Kai Sterling (Player 1)
        // One Pending invitation so the user can test Accept / Decline in My Hub!
        $pendingInvite = MatchInvitation::create([
            'match_id' => $qf2->id,
            'player_id' => $player1->id,
            'team_id' => $createdTeams['apex-sentinels-fc']->id,
            'status' => 'pending',
            'manager_notes' => 'Starting role for upcoming fixture against Titan Vanguard. Report to dressing room 90 mins prior to kickoff.',
        ]);

        $acceptedInvite = MatchInvitation::create([
            'match_id' => $liveMatch->id,
            'player_id' => $player1->id,
            'team_id' => $createdTeams['apex-sentinels-fc']->id,
            'status' => 'accepted',
            'manager_notes' => 'Confirmed start as primary winger in Quarterfinal 1.',
        ]);

        // Dispatch in-app notification to Kai Sterling
        $player1->notify(new MatchInvitationReceived($pendingInvite));

        // Invitations for   Demo (Player)
        $profPendingInvite = MatchInvitation::create([
            'match_id' => $qf2->id,
            'player_id' => $profPlayer->id,
            'team_id' => $createdTeams[' -titans-fc']->id,
            'status' => 'pending',
            'manager_notes' => 'Starting playmaker call-up for upcoming tournament showcase match. Accept to confirm starting XI slot.',
        ]);

        $profAcceptedInvite = MatchInvitation::create([
            'match_id' => $liveMatch->id,
            'player_id' => $profPlayer->id,
            'team_id' => $createdTeams[' -titans-fc']->id,
            'status' => 'accepted',
            'manager_notes' => 'Confirmed start as primary attacking playmaker in Quarterfinal fixture.',
        ]);

        $profPlayer->notify(new MatchInvitationReceived($profPendingInvite));

        // 7. News Articles & Disciplinary Incident Logs
        NewsArticle::create([
            'author_id' => $admin->id,
            'title' => 'Disciplinary Hearing: Match Incident #4 Conduct Review & Yellow Card Warning',
            'slug' => 'disciplinary-hearing-match-incident-4-conduct-review',
            'excerpt' => 'League Disciplinary Committee reviews late-game challenge in recent friendly fixture. Official sanctions and reprimand published.',
            'body' => "Following a comprehensive review of video evidence and the official match referee report from the clash between Apex Sentinels and Titan Vanguard, the League Disciplinary Committee has reached a binding determination.\n\nAt minute 82, a reckless sliding challenge occurred near the technical area resulting in a confrontation between technical staff. The committee found that while no violent conduct transpired, both clubs failed to exercise adequate bench discipline.\n\nSanctions Imposed:\n1. Formal written reprimand issued to Titan Vanguard management.\n2. Yellow card on file upheld without retro-active suspension.\n3. Mandatory fair-play protocol review mandated for all remaining knockout fixtures.\n\nThe league reiterates its zero-tolerance commitment to referee disrespect and unsportsmanlike conduct.",
            'match_incident_flag' => true,
            'match_id' => $compMatch->id,
            'published_at' => Carbon::now()->subHours(18),
        ]);

        NewsArticle::create([
            'author_id' => $admin->id,
            'title' => 'Referee Incident Log: Overtime Protocol Review in Division 1 Semi-Finals',
            'slug' => 'referee-incident-log-overtime-protocol-review',
            'excerpt' => 'Official clarification on sudden-death overtime rules and penalty shootout procedures heading into tournament finals.',
            'body' => "In accordance with revised 2026 League Tournament Regulations, any knockout game remaining level after 90 minutes of regulation play will proceed to two mandatory 15-minute extra time halves.\n\nShould scores remain tied after extra time, an official FIFA-standard 5-penalty shootout will determine the winner.\n\nAll club managers are instructed to familiarize their rosters with the sequence to prevent unnecessary delays during live broadcast windows.",
            'match_incident_flag' => true,
            'match_id' => $liveMatch->id,
            'published_at' => Carbon::now()->subDays(1),
        ]);

        NewsArticle::create([
            'author_id' => $admin->id,
            'title' => 'Apex Continental Championship 2026 Quarterfinal Brackets Confirmed',
            'slug' => 'apex-continental-championship-2026-quarterfinal-brackets-confirmed',
            'excerpt' => '8 elite clubs finalize their starting rosters as the knockout stage officially kicks off at the Metropolis Grand Olympic Stadium.',
            'body' => "The stage is set for the biggest championship in the region. After intense qualifying battles across the Metro Central, Pacific Coast, and Eastern Summit circuits, 8 championship-caliber teams have earned their place in the knockout bracket.\n\nLeading the charge are defending champions Apex Sentinels FC, steered by star forward Kai 'Phantom' Sterling. However, the defensive grit of Shadow Vipers United and the physical power of Titan Vanguard pose immediate threats to the throne.\n\nLive score updates, real-time minute-by-minute tickers, and official match telemetry will be broadcast exclusively across the LeagueHub platform.",
            'match_incident_flag' => false,
            'published_at' => Carbon::now()->subDays(2),
        ]);

        NewsArticle::create([
            'author_id' => $admin->id,
            'title' => 'Enhanced Athlete Health & Medical Hold Protocols Activated',
            'slug' => 'enhanced-athlete-health-medical-hold-protocols-activated',
            'excerpt' => 'New medical inspector dashboard gives league physicians the authority to place players on mandatory health hold.',
            'body' => "To safeguard player longevity, the league has deployed an integrated Medical Clearance and Injury Inspector module. Any player diagnosed with concussions, ligament sprains, or muscle tears is placed on an automatic medical hold.\n\nTeam managers cannot roster or field athletes on medical hold until the League Authority physician performs an official evaluation and clears the hold in the operations dashboard.",
            'match_incident_flag' => false,
            'published_at' => Carbon::now()->subDays(4),
        ]);

        // 8. Bookmarks for Viewer
        Bookmark::create([
            'user_id' => $viewer->id,
            'game_match_id' => $liveMatch->id,
        ]);
        Bookmark::create([
            'user_id' => $viewer->id,
            'game_match_id' => $qf2->id,
        ]);
        Bookmark::create([
            'user_id' => $viewer->id,
            'tournament_id' => $tournaments['apex-world-cup']->id,
        ]);

        // Bookmarks for   Demo (Viewer)
        Bookmark::create([
            'user_id' => $profViewer->id,
            'game_match_id' => $liveMatch->id,
        ]);
        Bookmark::create([
            'user_id' => $profViewer->id,
            'tournament_id' => $tournaments['apex-world-cup']->id,
        ]);

        // 9. Match result notification for Viewer
        $viewer->notify(new MatchResultNotification($compMatch));
    }
}
