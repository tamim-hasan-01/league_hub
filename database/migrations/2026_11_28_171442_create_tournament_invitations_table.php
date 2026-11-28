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
        if (! Schema::hasTable('tournament_invitations')) {
            Schema::create('tournament_invitations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
                $table->foreignId('manager_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('team_id')->nullable()->constrained()->cascadeOnDelete();
                $table->string('target_type', 20);
                $table->unsignedBigInteger('target_id');
                $table->foreignId('invited_by')->constrained('users')->cascadeOnDelete();
                $table->string('status')->default('pending');
                $table->timestamps();
            });
        }

        Schema::table('tournament_invitations', function (Blueprint $table) {
            $table->unique(
                ['tournament_id', 'target_type', 'target_id'],
                'tournament_invites_target_unique',
            );
            $table->index(['manager_id', 'status'], 'tournament_invites_manager_status_index');
            $table->index(['tournament_id', 'status'], 'tournament_invites_tournament_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_invitations');
    }
};
