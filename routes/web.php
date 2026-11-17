<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Api\LiveMatchController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyTournamentsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PlayerStatsController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;

// ==========================================
// 1. Public Discovery Endpoints
// General landing page and developer attribution information
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');

// ==========================================
// 2. Authentication Flow (Guest Only)
// Registration, account authentication, and administrator login gates
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::POST('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::POST('/login', [AuthenticatedSessionController::class, 'store']);
    Route::POST('/admin-login', [AuthenticatedSessionController::class, 'storeAdmin'])->name('login.admin');
});

// User session logout
Route::POST('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ==========================================
// 3. Public Directories & Resource Browsing
// Accessible by all users including unauthenticated visitors
// ==========================================

// Tournaments Directory & Details
Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
Route::get('/tournaments/{slug}', [TournamentController::class, 'show'])->name('tournaments.show');

// Teams Directory & Profiles
Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
Route::get('/teams/{slug}', [TeamController::class, 'show'])->name('teams.show');

// News & Disciplinary Incidents Directory
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

// ==========================================
// 4. Authenticated Member Features
// Requires active login session for participation and operations
// ==========================================
Route::middleware('auth')->group(function () {
    // Tournament Management & Entry Applications
    Route::POST('/tournaments', [TournamentController::class, 'store'])->name('tournaments.store');
    Route::POST('/tournaments/{tournament}/apply', [TournamentController::class, 'apply'])->name('tournaments.apply');
    Route::POST('/tournaments/{tournament}/generate-bracket', [TournamentController::class, 'generateBracket'])->name('tournaments.generateBracket');

    // Team Management & Squad Operations
    Route::get('/teams-create/new', [TeamController::class, 'create'])->name('teams.create');
    Route::POST('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::get('/teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit');
    Route::put('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
    Route::POST('/teams/{team}/add-player', [TeamController::class, 'addPlayer'])->name('teams.addPlayer');
    Route::delete('/teams/{team}/remove-player/{player}', [TeamController::class, 'removePlayer'])->name('teams.removePlayer');
    Route::POST('/teams/{team}/propose-friendly', [TeamController::class, 'proposeFriendly'])->name('teams.proposeFriendly');

    // Player Analytics & Medical Self-Reporting
    Route::get('/mystats', [PlayerStatsController::class, 'index'])->name('mystats');
    Route::POST('/mystats/health', [PlayerStatsController::class, 'toggleHealth'])->name('mystats.toggleHealth');

    // Personalized Member Hub & Match Bookmarks
    Route::get('/mytournaments', [MyTournamentsController::class, 'index'])->name('mytournaments');
    Route::POST('/invitations/{invitation}/respond', [MyTournamentsController::class, 'respondInvitation'])->name('invitations.respond');
    Route::POST('/bookmarks/toggle', [MyTournamentsController::class, 'toggleBookmark'])->name('bookmarks.toggle');

    // Official News Article Publishing
    Route::POST('/news', [NewsController::class, 'store'])->name('news.store');

    // In-App Notification Management
    Route::POST('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // ==========================================
    // 5. Authority Operations Dashboard
    // Guarded console for league organizers and administrators
    // ==========================================
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::POST('/matches/{match}/score', [AdminDashboardController::class, 'updateScore'])->name('matches.score');
        Route::POST('/applications/{application}/review', [AdminDashboardController::class, 'reviewApplication'])->name('applications.review');
        Route::POST('/players/{profile}/clear-injury', [AdminDashboardController::class, 'clearInjury'])->name('players.clearInjury');
    });
});

// ==========================================
// 6. Real-Time REST API Endpoints
// Live match ticker polling and single fixture payload serialization
// ==========================================
Route::get('/api/matches/ticker', [LiveMatchController::class, 'ticker'])->name('api.matches.ticker');
Route::get('/api/matches/{match}/live', [LiveMatchController::class, 'show'])->name('api.matches.live');
