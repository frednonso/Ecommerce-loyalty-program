<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Order matters:
     *  1. Achievements & badges must exist before users trigger the pipeline.
     *  2. UserSeeder creates Purchase records through the model so
     *     $dispatchesEvents fires PurchaseCompleted → achievements & badges
     *     are awarded exactly as they are in production.
     *
     * WithoutModelEvents is intentionally NOT used so that every Purchase
     * created here goes through the full event/listener chain.
     */
    public function run(): void
    {
        $this->call([
            AchievementSeeder::class,   // 6 achievements
            BadgeSeeder::class,         // 5 badges
            UserSeeder::class,          // 3 demo users + purchases
        ]);
    }
}
