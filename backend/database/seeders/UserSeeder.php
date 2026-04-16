<?php

namespace Database\Seeders;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Three demo users with varying purchase counts so every dashboard state
     * (new user, mid-progress, advanced) is immediately testable.
     *
     * All passwords are 'password'.
     *
     * Purchases are inserted directly via the Purchase model so that
     * $dispatchesEvents fires for each one and the achievement/badge pipeline
     * runs exactly as it would in production.
     *
     * Note: WithoutModelEvents is NOT used in DatabaseSeeder so events fire.
     */
    public function run(): void
    {
        $users = [
            [
                'name'            => 'Alice Newbie',
                'email'           => 'alice@example.com',
                'purchase_count'  => 1,
                // Expect: "First Purchase" achievement, "Beginner" badge
            ],
            [
                'name'            => 'Bob Midway',
                'email'           => 'bob@example.com',
                'purchase_count'  => 7,
                // Expect: "First Purchase" + "5 Purchases", "Bronze" badge
            ],
            [
                'name'            => 'Carol Advanced',
                'email'           => 'carol@example.com',
                'purchase_count'  => 26,
                // Expect: first 4 achievements, "Gold" badge
            ],
        ];

        foreach ($users as $userData) {
            // firstOrCreate keeps the seeder idempotent on re-runs.
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name'     => $userData['name'],
                    'password' => Hash::make('password'),
                ],
            );

            // Only create purchases if the user has none yet (idempotency).
            if ($user->purchases()->count() === 0) {
                for ($i = 0; $i < $userData['purchase_count']; $i++) {
                    // Random amount between ₦500 – ₦10,000.
                    Purchase::create([
                        'user_id' => $user->id,
                        'amount'  => fake()->numberBetween(500, 10000),
                    ]);
                }
            }
        }
    }
}
