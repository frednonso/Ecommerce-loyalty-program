<?php

namespace App\Events;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BadgeUnlocked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The user who just earned the badge.
     */
    public User $user;

    /**
     * The badge that was earned.
     */
    public Badge $badge;

    /**
     * Create a new event instance.
     *
     * Fired when a new badge threshold is crossed in ProcessAchievementsAndBadges.
     */
    public function __construct(User $user, Badge $badge)
    {
        $this->user  = $user;
        $this->badge = $badge;
    }
}
