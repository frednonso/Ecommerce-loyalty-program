<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserProgressService;
use Illuminate\Http\JsonResponse;

class UserAchievementController extends Controller
{
    public function __construct(
        protected UserProgressService $progressService,
    ) {}

    /**
     * GET /api/users/{user}/achievements
     *
     * Returns the user's unlocked achievements, remaining achievements,
     * current badge, next badge, and how many more achievements are needed
     * to reach the next badge.
     */
    public function show(User $user): JsonResponse
    {
        $data = $this->progressService->getProgress($user);

        return response()->json([
            'success' => true,
            'message' => 'User progress retrieved successfully.',
            'data'    => $data,
        ]);
    }
}
