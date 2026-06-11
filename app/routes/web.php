<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CareersController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\PublicAuthController;
use App\Http\Controllers\QuoteAcceptController;
use Illuminate\Support\Facades\Route;

// Public marketing site — Vue + Inertia (frontend/_index.md).
Route::get('/', [MarketingController::class, 'home'])->name('home');
Route::get('/pricing', [MarketingController::class, 'pricing'])->name('pricing');
Route::get('/features', [MarketingController::class, 'features'])->name('features');
Route::get('/product/{domain}', [MarketingController::class, 'domain'])->name('product.domain');
Route::get('/sitemap.xml', [MarketingController::class, 'sitemap']);
Route::get('/about', [MarketingController::class, 'about'])->name('about');
Route::get('/contact', [MarketingController::class, 'contact'])->name('contact');
Route::post('/contact', [MarketingController::class, 'submitContact'])->middleware('throttle:10,1');
Route::get('/terms', [MarketingController::class, 'terms'])->name('terms');
Route::get('/privacy', [MarketingController::class, 'privacy'])->name('privacy');

// Public auth — Vue + Inertia, throttled like Filament logins.
Route::middleware('throttle:10,1')->group(function (): void {
    Route::get('/login', [PublicAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [PublicAuthController::class, 'login']);
    Route::post('/logout', [PublicAuthController::class, 'logout'])->name('logout');
    Route::get('/forgot-password', [PublicAuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [PublicAuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PublicAuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [PublicAuthController::class, 'resetPassword'])->name('password.update');
});

// Invite-only registration — the single public registration surface.
// Rate-limited like login per architecture/security.
Route::middleware('throttle:5,1')->group(function (): void {
    Route::get('/register/invite/{token}', [AuthController::class, 'showInviteRegistration']);
    Route::post('/register/invite/{token}', [AuthController::class, 'acceptInvite']);
});

// Public quote acceptance — guest guard, single-use token, throttled
// (crm.quotes security notes).
Route::middleware('throttle:10,1')->group(function (): void {
    Route::get('/quotes/accept/{token}', [QuoteAcceptController::class, 'show']);
    Route::post('/quotes/accept/{token}', [QuoteAcceptController::class, 'accept']);
});

// Public careers pages — guest, throttled, honeypot (hr.recruitment).
Route::middleware('throttle:10,1')->group(function (): void {
    Route::get('/careers/{slug}', [CareersController::class, 'show']);
    Route::post('/careers/{slug}/apply', [CareersController::class, 'apply']);
});
