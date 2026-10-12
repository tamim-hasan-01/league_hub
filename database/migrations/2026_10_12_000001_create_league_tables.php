<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('player_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('jersey_number')->nullable();
            $table->string('primary_sport')->default('Football');
            $table->boolean('is_injured')->default(false);
            $table->text('injury_notes')->nullable();
            $table->integer('areal_ranking')->default(100);
            $table->json('career_stats')->nullable();
            $table->timestamps();
        });

        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manager_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->string('sport_type')->default('Football');
            $table->integer('areal_ranking')->default(50);
            $table->text('description')->nullable();
            $table->string('home_venue')->nullable();
            $table->timestamps();
        });

        Schema::create('team_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('position')->default('Player');
            $table->string('status')->default('active'); // active, bench, reserve
            $table->timestamps();
        });

        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('sport_type')->default('Football');
            $table->string('status')->default('registration_open'); // draft, registration_open, in_progress, completed
            $table->string('current_stage')->default('group'); // group, quarter_final, semi_final, final
            $table->string('banner')->nullable();
            $table->text('description')->nullable();
            $table->string('venue')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('prize_pool')->nullable();
            $table->integer('max_teams')->default(8);
            $table->timestamps();
        });

        Schema::create('tournament_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('game_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('home_team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('away_team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('stage')->default('friendly'); // group, quarter_final, semi_final, final, friendly
            $table->dateTime('scheduled_at');
            $table->string('venue');
            $table->string('status')->default('upcoming'); // upcoming, live, completed
            $table->integer('home_score')->default(0);
            $table->integer('away_score')->default(0);
            $table->json('match_data')->nullable(); // periods, timeline events, scores, mvp
            $table->timestamps();
        });

        Schema::create('match_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('game_matches')->cascadeOnDelete();
            $table->foreignId('player_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, accepted, declined
            $table->text('manager_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->string('cover_image')->nullable();
            $table->boolean('match_incident_flag')->default(false);
            $table->foreignId('match_id')->nullable()->constrained('game_matches')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_match_id')->nullable()->constrained('game_matches')->cascadeOnDelete();
            $table->foreignId('tournament_id')->nullable()->constrained('tournaments')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
        Schema::dropIfExists('news_articles');
        Schema::dropIfExists('match_invitations');
        Schema::dropIfExists('game_matches');
        Schema::dropIfExists('tournament_applications');
        Schema::dropIfExists('tournaments');
        Schema::dropIfExists('team_user');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('player_profiles');
    }
};

