<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Badge;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAchievementsApiTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Seed the canonical achievements and badges that the business logic
     * depends on. Mirrored from AchievementSeeder / BadgeSeeder so tests are
     * self-contained and don't rely on running db:seed.
     */
    private function seedLookupData(): void
    {
        $achievements = [
            ['name' => 'First Purchase',  'required_purchases' => 1],
            ['name' => '5 Purchases',     'required_purchases' => 5],
            ['name' => '10 Purchases',    'required_purchases' => 10],
            ['name' => '25 Purchases',    'required_purchases' => 25],
            ['name' => '50 Purchases',    'required_purchases' => 50],
            ['name' => '100 Purchases',   'required_purchases' => 100],
        ];
        foreach ($achievements as $a) {
            Achievement::create($a);
        }

        $badges = [
            ['name' => 'Beginner',  'min_achievements' => 1],
            ['name' => 'Bronze',    'min_achievements' => 2],
            ['name' => 'Silver',    'min_achievements' => 3],
            ['name' => 'Gold',      'min_achievements' => 4],
            ['name' => 'Platinum',  'min_achievements' => 5],
        ];
        foreach ($badges as $b) {
            Badge::create($b);
        }
    }

    /** Create a user and give them $count purchases, returning the user. */
    private function userWithPurchases(int $count): User
    {
        $user = User::factory()->create();

        for ($i = 0; $i < $count; $i++) {
            Purchase::create(['user_id' => $user->id, 'amount' => 1000]);
        }

        return $user;
    }

    // ── Tests ─────────────────────────────────────────────────────────────────

    /**
     * A brand-new user (0 purchases) should get an empty unlocked list,
     * all 6 achievements as next_available, "Beginner" as current badge
     * (the fallback string), and the correct remaining count.
     */
    public function test_returns_correct_structure_for_new_user(): void
    {
        $this->seedLookupData();

        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}/achievements");

        $response->assertOk()
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'unlocked_achievements',
                         'next_available_achievements',
                         'current_badge',
                         'next_badge',
                         'remaining_to_unlock_next_badge',
                     ],
                 ])
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.unlocked_achievements', [])
                 ->assertJsonCount(6, 'data.next_available_achievements')
                 ->assertJsonPath('data.current_badge', 'Beginner')
                 ->assertJsonPath('data.next_badge', 'Beginner')   // first badge not yet earned
                 ->assertJsonPath('data.remaining_to_unlock_next_badge', 1);
    }

    /**
     * After 5 purchases the user should have "First Purchase" and
     * "5 Purchases" in unlocked_achievements (QUEUE_CONNECTION=sync means
     * the listener runs immediately when Purchase::create() is called).
     */
    public function test_unlocked_achievements_after_purchases(): void
    {
        $this->seedLookupData();

        $user = $this->userWithPurchases(5);

        $response = $this->getJson("/api/users/{$user->id}/achievements");

        $response->assertOk();

        $unlocked = $response->json('data.unlocked_achievements');

        $this->assertContains('First Purchase', $unlocked);
        $this->assertContains('5 Purchases',    $unlocked);
        $this->assertCount(2, $unlocked);
    }

    /**
     * Badge advancement: 1 purchase → Beginner, 5 purchases → Bronze,
     * 10 purchases → Silver.
     */
    public function test_current_badge_advances_correctly(): void
    {
        $this->seedLookupData();

        // 1 purchase → 1 achievement → Beginner badge
        $userA = $this->userWithPurchases(1);
        $this->getJson("/api/users/{$userA->id}/achievements")
             ->assertJsonPath('data.current_badge', 'Beginner');

        // 5 purchases → 2 achievements → Bronze badge
        $userB = $this->userWithPurchases(5);
        $this->getJson("/api/users/{$userB->id}/achievements")
             ->assertJsonPath('data.current_badge', 'Bronze');

        // 10 purchases → 3 achievements → Silver badge
        $userC = $this->userWithPurchases(10);
        $this->getJson("/api/users/{$userC->id}/achievements")
             ->assertJsonPath('data.current_badge', 'Silver');
    }

    /**
     * remaining_to_unlock_next_badge must equal
     * next_badge.min_achievements − user's current unlocked count.
     *
     * User has 5 purchases → 2 unlocked → current: Bronze (min 2).
     * Next badge: Silver (min 3). Remaining = 3 − 2 = 1.
     */
    public function test_remaining_to_next_badge_is_accurate(): void
    {
        $this->seedLookupData();

        $user = $this->userWithPurchases(5);

        $response = $this->getJson("/api/users/{$user->id}/achievements");

        $response->assertOk()
                 ->assertJsonPath('data.current_badge', 'Bronze')
                 ->assertJsonPath('data.next_badge', 'Silver')
                 ->assertJsonPath('data.remaining_to_unlock_next_badge', 1);
    }
}
