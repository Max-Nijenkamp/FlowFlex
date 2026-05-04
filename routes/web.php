<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::redirect('/login', '/workspace/login')->name('login');
