<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

/**
 * TournamentQueryFilter
 *
 * Encapsulates dynamic query filtering for tournament directory browsing,
 * supporting status filtering, sport types, search keywords, and date ranges.
 */
class TournamentQueryFilter
{
    /**
     * Apply an array of user filter inputs to the given Tournament Eloquent builder.
     */
    public static function apply(Builder $query, array $filters): Builder
    {
        // Filter by tournament status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by sport type (e.g. Football, Basketball, Cricket, Esports)
        if (!empty($filters['sport_type'])) {
            $query->where('sport_type', $filters['sport_type']);
        }

        // Text search across title, venue, and description
        if (!empty($filters['search'])) {
            $search = '%' . trim($filters['search']) . '%';
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('venue', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }

        // Filter by start date range
        if (!empty($filters['date_from'])) {
            $query->whereDate('start_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('start_date', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * Provide predefined list of available tournament status filter options.
     */
    public static function statusOptions(): array
    {
        return [
            'registration_open' => 'Registration Open',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
        ];
    }

    /**
     * Provide predefined list of supported sports categories.
     */
    public static function sportOptions(): array
    {
        return [
            'Football' => 'Football',
            'Basketball' => 'Basketball',
            'Cricket' => 'Cricket',
            'Volleyball' => 'Volleyball',
            'Esports' => 'Esports',
        ];
    }
}
