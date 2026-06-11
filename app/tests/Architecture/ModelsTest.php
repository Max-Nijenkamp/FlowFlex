<?php

declare(strict_types=1);
use App\Models\Admin;
use App\Models\User;

// Model-layer conventions. Tenant models gain BelongsToCompany as domains ship;
// these baseline rules hold from Foundation onward.

arch('models extend Eloquent or Authenticatable')
    ->expect('App\Models')
    ->toExtend('Illuminate\Database\Eloquent\Model')
    ->ignoring([
        User::class,
        Admin::class,
    ]);

arch('auth models are authenticatable')
    ->expect([User::class, Admin::class])
    ->toExtend('Illuminate\Foundation\Auth\User');

arch('middleware are classes')
    ->expect('App\Http\Middleware')
    ->toBeClasses();
