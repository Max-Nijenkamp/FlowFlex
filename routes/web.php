<?php

use App\Http\Controllers\Marketing\ContactController;
use App\Http\Controllers\Marketing\DemoController;
use App\Http\Controllers\Marketing\MarketingController;
use App\Http\Controllers\Marketing\StatusController;
use Illuminate\Support\Facades\Route;

// Homepage
Route::get('/', [MarketingController::class, 'home'])->name('home');

// Redirect self-registration attempts to demo
Route::redirect('/register', '/demo')->name('register');
Route::redirect('/signup', '/demo');
Route::redirect('/trial', '/demo');
Route::redirect('/login', '/workspace/login')->name('login');

// Marketing pages
Route::get('/pricing', [MarketingController::class, 'pricing'])->name('pricing');
Route::get('/features', [MarketingController::class, 'features'])->name('features');
Route::get('/about', [MarketingController::class, 'about'])->name('about');
Route::get('/contact', [MarketingController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/careers', [MarketingController::class, 'careers'])->name('careers');
Route::get('/changelog', [MarketingController::class, 'changelog'])->name('changelog');
Route::get('/status', StatusController::class)->name('status');

// Demo request
Route::get('/demo', [DemoController::class, 'show'])->name('demo');
Route::post('/demo', [DemoController::class, 'store'])->name('demo.store');

// Blog
Route::get('/blog', [MarketingController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [MarketingController::class, 'blogPost'])->name('blog.post');

// Legal
Route::prefix('legal')->name('legal.')->group(function () {
    Route::get('/privacy', [MarketingController::class, 'privacy'])->name('privacy');
    Route::get('/terms', [MarketingController::class, 'terms'])->name('terms');
    Route::get('/cookies', [MarketingController::class, 'cookies'])->name('cookies');
    Route::get('/dpa', [MarketingController::class, 'dpa'])->name('dpa');
    Route::get('/aup', [MarketingController::class, 'aup'])->name('aup');
});
Route::get('/security', [MarketingController::class, 'security'])->name('security');

// Help centre
Route::get('/help', [MarketingController::class, 'help'])->name('help');
Route::get('/help/{slug}', [MarketingController::class, 'helpArticle'])->name('help.article');

// Module detail pages
Route::get('/modules/{key}', [MarketingController::class, 'moduleDetail'])->name('modules.show');
