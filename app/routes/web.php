<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\InviteRegistrationController;
use App\Http\Controllers\Marketing\ContactController;
use App\Http\Controllers\Media\MediaDownloadController;
use App\Http\Middleware\SetCompanyContext;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ── Public marketing site (Vue + Inertia, Switchboard+ design) ──────────────
Route::get('/', fn () => Inertia::render('Marketing/Home'))->name('home');
Route::get('/pricing', fn () => Inertia::render('Marketing/Pricing'))->name('pricing');
Route::get('/product', fn () => Inertia::render('Marketing/Product'))->name('product');
Route::get('/product/{domain}', function (string $domain) {
    abort_unless(in_array($domain, ['hr', 'finance', 'crm', 'projects'], true), 404);

    return Inertia::render('Marketing/Domain', ['domain' => $domain]);
})->name('product.domain');
Route::get('/about', fn () => Inertia::render('Marketing/About'))->name('about');
Route::get('/contact', fn () => Inertia::render('Marketing/Contact'))->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');
Route::get('/terms', fn () => Inertia::render('Marketing/Terms'))->name('terms');
Route::get('/privacy', fn () => Inertia::render('Marketing/Privacy'))->name('privacy');
Route::get('/modules', fn () => Inertia::render('Marketing/Modules'))->name('modules');
Route::get('/switch', fn () => Inertia::render('Marketing/SwitchOver'))->name('switch');
Route::get('/trust', fn () => Inertia::render('Marketing/Trust'))->name('trust');
Route::get('/changelog', fn () => Inertia::render('Marketing/Changelog'))->name('changelog');
Route::get('/calculator', fn () => Inertia::render('Marketing/Calculator'))->name('calculator');
Route::get('/customers/veldkamp-logistics', fn () => Inertia::render('Marketing/CaseStudy'))->name('case-study');
Route::get('/help', fn () => Inertia::render('Marketing/Help'))->name('help');
Route::get('/status', fn () => Inertia::render('Marketing/Status', [
    'checkedAt' => now()->timezone('Europe/Amsterdam')->format('d M Y · H:i').' CEST',
]))->name('status');

// Workspace login entry point: the panel login carries the designed screen
// (login parity, frontend/design-system.md) — the marketing "Sign in" links
// land there directly.
Route::redirect('/login', '/app/login')->name('login');

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

// Branded 404 for any unmatched public GET ("this page is switched off") —
// panel routes never reach this fallback.
Route::fallback(fn () => Inertia::render('Marketing/NotFound')->toResponse(request())->setStatusCode(404));
