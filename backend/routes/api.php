<?php

use App\Http\Controllers\Api\UserAchievementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the Application via bootstrap/app.php and are
| automatically prefixed with /api by the withRouting() call there.
|
*/

// GET /api/users/{user}/achievements
// Returns the user's full achievement & badge progress summary.
Route::get('/users/{user}/achievements', [UserAchievementController::class, 'show'])
    ->name('users.achievements');
