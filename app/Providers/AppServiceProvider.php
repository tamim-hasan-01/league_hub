<?php

namespace App\Providers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('access-dashboard', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-team', function (User $user, Team $team) {
            return $user->isAdmin() || ($user->isManager() && $team->manager_id === $user->id);
        });

        Gate::define('create-tournament', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('propose-friendly', function (User $user) {
            return $user->isAdmin() || $user->isManager();
        });

        Gate::define('apply-tournament', function (User $user, Team $team) {
            return $user->isAdmin() || ($user->isManager() && $team->manager_id === $user->id);
        });

        Gate::define('manage-user', function (User $actor, User $target) {
            if ($target->isAdmin()) {
                return false;
            }

            if ($actor->isAdmin()) {
                return true;
            }

            return $actor->isManager() && in_array($target->role, ['player', 'viewer'], true);
        });

        Gate::define('player-only', function (User $user) {
            return $user->isPlayer() || $user->isAdmin();
        });

        // Share unread notifications with views when user is authenticated
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $view->with('unreadNotificationsCount', auth()->user()->unreadNotifications()->count());
            } else {
                $view->with('unreadNotificationsCount', 0);
            }
        });
    }
}
