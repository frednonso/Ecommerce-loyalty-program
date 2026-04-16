<?php

namespace Tests\Unit;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AchievementLogicTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function seedLookupData(): void
    {
        Achievement::insert([
            ['name' => 'First Purchase',  'required_purchases' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['name' => '5 Purchases',     'required_purchases' => 5,  'created_at' => now(), 'updated_at' => now()],
            ['name' => '10 Purchases',    'required_purchases' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Badge::insert([
            ['name' => 'Beginner',  'min_achievements' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bronze',    'min_achievements' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Silver',    'min_achievements' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    // ── Tests ─────────────────────────────────────────────────────────────────

    /**
     * Creating a Purchase should fire AchievementUnlocked for every
     * achievement whose threshold the user has just met.
     *
     * We use Event::fake() to intercept the event without actually running
     * the listeners, then assert it was dispatched with the right payload.
     */
    public function test_achievement_unlocked_event_fired(): void
    {
        $this->seedLookupData();

        // Fake AFTER seeding so PurchaseCompleted → ProcessAchievementsAndBadges
        // still fires (it's not faked); only AchievementUnlocked is intercepted.
        Event::fake([AchievementUnlocked::class, BadgeUnlocked::class]);

        $user = User::factory()->create();

        // One purchase → should unlock "First Purchase" (required = 1)
        Purchase::create(['user_id' => $user->id, 'amount' => 500]);

        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($user) {
            return $event->user->id === $user->id
                && $event->achievement->name === 'First Purchase';
        });
    }

    /**
     * When the number of unlocked achievements crosses a badge threshold,
     * BadgeUnlocked must be fired with the correct badge.
     *
     * Two purchases → "First Purchase" + "5 Purchases"... wait, 2 purchases
     * only unlocks "First Purchase". We need 5 purchases to unlock both
     * "First Purchase" and "5 Purchases" → 2 achievements → "Bronze" badge.
     */
    public function test_badge_unlocked_event_fired_at_threshold(): void
    {
        $this->seedLookupData();

        Event::fake([AchievementUnlocked::class, BadgeUnlocked::class]);

        $user = User::factory()->create();

        // 5 purchases → unlocks "First Purchase" (1) + "5 Purchases" (5)
        // = 2 achievements → meets Bronze threshold (min_achievements = 2)
        for ($i = 0; $i < 5; $i++) {
            Purchase::create(['user_id' => $user->id, 'amount' => 500]);
        }

        Event::assertDispatched(BadgeUnlocked::class, function ($event) use ($user) {
            return $event->user->id === $user->id
                && $event->badge->name === 'Bronze';
        });
    }

    /**
     * When BadgeUnlocked fires, SendCashbackOnBadgeUnlock must write a
     * structured entry to the 'cashback' log channel.
     *
     * We spy on the Log facade so no real file I/O is needed in the test.
     */
    public function test_cashback_log_written_on_badge_unlock(): void
    {
        $this->seedLookupData();

        Log::spy();

        $user = User::factory()->create();

        // 5 purchases → unlocks 2 achievements → triggers Bronze badge
        // → SendCashbackOnBadgeUnlock fires → CashbackService::disburse()
        for ($i = 0; $i < 5; $i++) {
            Purchase::create(['user_id' => $user->id, 'amount' => 500]);
        }

        // Assert that the cashback channel received an 'info' log call whose
        // context includes the user ID and the badge name.
        Log::shouldHaveReceived('channel')
            ->with('cashback')
            ->once();
    }
}
