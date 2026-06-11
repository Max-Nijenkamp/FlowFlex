<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Invite-only registration — the single public registration surface.
// Rate-limited like login per architecture/security.
Route::middleware('throttle:5,1')->group(function (): void {
    Route::get('/register/invite/{token}', [AuthController::class, 'showInviteRegistration']);
    Route::post('/register/invite/{token}', [AuthController::class, 'acceptInvite']);
});
