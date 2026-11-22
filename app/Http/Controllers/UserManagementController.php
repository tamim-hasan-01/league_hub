<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserManagementController extends Controller
{
    public function suspend(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('manage-user', $user);

        $validated = $request->validate([
            'match_count' => ['required', 'integer', 'in:1,2,3'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $user->update([
            'account_status' => 'suspended',
            'suspension_matches_remaining' => $validated['match_count'],
            'suspension_reason' => $validated['reason'],
            'suspended_at' => now(),
            'suspended_by' => $request->user()->id,
        ]);

        return back()->with('success', "{$user->name} was suspended for {$validated['match_count']} match(es).");
    }

    public function restore(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('manage-user', $user);

        $user->update([
            'account_status' => 'active',
            'suspension_matches_remaining' => 0,
            'suspension_reason' => null,
            'suspended_at' => null,
            'suspended_by' => null,
        ]);

        return back()->with('success', "{$user->name} account was restored.");
    }
}
