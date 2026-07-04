<?php

declare(strict_types=1);

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
