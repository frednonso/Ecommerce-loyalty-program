<?php

namespace App\Providers;

use App\Events\BadgeUnlocked;
use App\Events\PurchaseCompleted;
use App\Listeners\ProcessAchievementsAndBadges;
use App\Listeners\SendCashbackOnBadgeUnlock;
use Illuminate\Support\Facades\Event;
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
     *
     * Event → Listener mappings are registered here because Laravel 11 ships
     * without a standalone EventServiceProvider. Add new listeners to the
     * relevant Event::listen() call, or add new Event::listen() blocks below.
     */
    public function boot(): void
    {
        // When a purchase is created the Purchase model fires PurchaseCompleted.
        // This listener checks for newly unlocked achievements and badges,
        // then fires AchievementUnlocked / BadgeUnlocked as needed.
        Event::listen(
            PurchaseCompleted::class,
            ProcessAchievementsAndBadges::class,
        );

        // When a new badge is earned, send the fixed ₦300 cashback reward.
        Event::listen(
            BadgeUnlocked::class,
            SendCashbackOnBadgeUnlock::class,
        );
    }
}
