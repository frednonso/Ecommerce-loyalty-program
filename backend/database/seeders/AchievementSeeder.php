<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Six achievements in ascending purchase-count order.
     *
     * required_purchases is the exact number of purchases a user must have
     * made to unlock the achievement.
     */
    public function run(): void
    {
        $achievements = [
            ['name' => 'First Purchase',    'required_purchases' => 1],
            ['name' => '5 Purchases',       'required_purchases' => 5],
            ['name' => '10 Purchases',      'required_purchases' => 10],
            ['name' => '25 Purchases',      'required_purchases' => 25],
            ['name' => '50 Purchases',      'required_purchases' => 50],
            ['name' => '100 Purchases',     'required_purchases' => 100],
        ];

        foreach ($achievements as $achievement) {
            Achievement::firstOrCreate(
                ['name' => $achievement['name']],
                $achievement,
            );
        }
    }
}
