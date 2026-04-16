<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Events\PurchaseCompleted;
use App\Models\Achievement;
use App\Models\Badge;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessAchievementsAndBadges implements ShouldQueue
{
    /**
     * Handle the PurchaseCompleted event.
     *
     * Flow:
     *  1. Count the user's total purchases.
     *  2. Find all achievements the user hasn't unlocked yet but now qualifies for.
     *  3. Attach each new achievement to the pivot table and fire AchievementUnlocked.
     *  4. Count the user's total unlocked achievements.
     *  5. Find the highest badge the user hasn't earned yet but now qualifies for.
     *  6. Attach that badge to the pivot table and fire BadgeUnlocked.
     */
    public function handle(PurchaseCompleted $event): void
    {
        $user = $event->purchase->user;

        // ── Step 1: total purchases ──────────────────────────────────────────
        $purchaseCount = $user->purchases()->count();

        // ── Steps 2 & 3: new achievements ───────────────────────────────────
        // Collect the IDs of achievements the user already holds so we can
        // exclude them from the query without an extra join.
        $unlockedAchievementIds = $user->achievements()->pluck('achievements.id');

        $newAchievements = Achievement::where('required_purchases', '<=', $purchaseCount)
            ->whereNotIn('id', $unlockedAchievementIds)
            ->get();

        foreach ($newAchievements as $achievement) {
            // Attach to pivot (unlocked_at defaults to NOW() in the migration)
            $user->achievements()->attach($achievement->id, [
                'unlocked_at' => now(),
            ]);

            AchievementUnlocked::dispatch($user, $achievement);
        }

        // ── Step 4: total unlocked achievements ─────────────────────────────
        // Reload the count fresh so it includes the ones we just attached.
        $achievementCount = $user->achievements()->count();

        // ── Steps 5 & 6: new badge ──────────────────────────────────────────
        $unlockedBadgeIds = $user->badges()->pluck('badges.id');

        // Find the single highest-threshold badge the user now qualifies for.
        $newBadge = Badge::where('min_achievements', '<=', $achievementCount)
            ->whereNotIn('id', $unlockedBadgeIds)
            ->orderByDesc('min_achievements')
            ->first();

        if ($newBadge) {
            $user->badges()->attach($newBadge->id, [
                'unlocked_at' => now(),
            ]);

            BadgeUnlocked::dispatch($user, $newBadge);
        }
    }
}
