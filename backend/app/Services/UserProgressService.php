<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\Achievement;
use App\Models\User;

class UserProgressService
{
    /**
     * Build the full achievement & badge progress summary for a given user.
     *
     * @return array{
     *   unlocked_achievements: array<string>,
     *   next_available_achievements: array<string>,
     *   current_badge: string,
     *   next_badge: string|null,
     *   remaining_to_unlock_next_badge: int|null,
     * }
     */
    public function getProgress(User $user): array
    {
        // ── Achievements ─────────────────────────────────────────────────────

        // IDs the user has already unlocked (used to exclude them below).
        $unlockedIds = $user->achievements()->pluck('achievements.id');

        $unlockedNames = $user->achievements()
            ->pluck('achievements.name')
            ->values()
            ->all();

        $nextAvailable = Achievement::whereNotIn('id', $unlockedIds)
            ->orderBy('required_purchases')
            ->pluck('name')
            ->values()
            ->all();

        // ── Badges ───────────────────────────────────────────────────────────

        $unlockedCount = count($unlockedNames);

        // The current badge is the highest one the user has earned.
        // Fall back to "Beginner" if none have been earned yet.
        $currentBadge = $user->badges()
            ->orderByDesc('min_achievements')
            ->first();

        $currentBadgeName = $currentBadge ? $currentBadge->name : 'Beginner';

        // The next badge is the lowest-threshold badge above the current level.
        $currentThreshold = $currentBadge ? $currentBadge->min_achievements : -1;

        $nextBadge = Badge::where('min_achievements', '>', $currentThreshold)
            ->orderBy('min_achievements')
            ->first();

        $nextBadgeName              = $nextBadge ? $nextBadge->name : null;
        $remainingToUnlockNextBadge = $nextBadge
            ? max(0, $nextBadge->min_achievements - $unlockedCount)
            : null;

        return [
            'unlocked_achievements'          => $unlockedNames,
            'next_available_achievements'    => $nextAvailable,
            'current_badge'                  => $currentBadgeName,
            'next_badge'                     => $nextBadgeName,
            'remaining_to_unlock_next_badge' => $remainingToUnlockNextBadge,
        ];
    }
}
