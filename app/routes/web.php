<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\InviteRegistrationController;
use App\Http\Controllers\Media\MediaDownloadController;
use App\Http\Middleware\SetCompanyContext;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Pre-signed media downloads (core.file-storage): signature checked in the
// controller, tenant ownership via SetCompanyContext + company_id match.
Route::get('/media/{media}/download', MediaDownloadController::class)
    ->middleware(['web', 'auth', SetCompanyContext::class])
    ->name('media.download');

// Public invite registration (core.invitation-system) - throttled: public
// endpoint touching auth (architecture/security.md public-endpoint limiters).
Route::middleware('throttle:20,1')->group(function (): void {
    Route::get('/register/invite/{token}', [InviteRegistrationController::class, 'show'])
        ->name('invite.register');
    Route::post('/register/invite/{token}', [InviteRegistrationController::class, 'store'])
        ->name('invite.register.store');
});
