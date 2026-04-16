<?php

namespace App\Events;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AchievementUnlocked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The user who just unlocked the achievement.
     */
    public User $user;

    /**
     * The achievement that was unlocked.
     */
    public Achievement $achievement;

    /**
     * Create a new event instance.
     *
     * Fired once per new achievement inside ProcessAchievementsAndBadges.
     */
    public function __construct(User $user, Achievement $achievement)
    {
        $this->user        = $user;
        $this->achievement = $achievement;
    }
}
