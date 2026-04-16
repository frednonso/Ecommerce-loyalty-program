<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Five badges in ascending achievement-count order.
     *
     * min_achievements is the minimum number of unlocked achievements a user
     * must hold before this badge is awarded.
     */
    public function run(): void
    {
        $badges = [
            ['name' => 'Beginner',  'min_achievements' => 1],
            ['name' => 'Bronze',    'min_achievements' => 2],
            ['name' => 'Silver',    'min_achievements' => 3],
            ['name' => 'Gold',      'min_achievements' => 4],
            ['name' => 'Platinum',  'min_achievements' => 5],
        ];

        foreach ($badges as $badge) {
            Badge::firstOrCreate(
                ['name' => $badge['name']],
                $badge,
            );
        }
    }
}
