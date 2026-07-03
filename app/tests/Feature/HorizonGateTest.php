<?php

declare(strict_types=1);

use App\Models\Admin;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

test('horizon gate admits admins and rejects tenant users + guests', function () {
    $admin = Admin::factory()->create();
    $user = User::factory()->for(Company::factory())->create();

    expect(Gate::forUser($admin)->check('viewHorizon'))->toBeTrue()
        ->and(Gate::forUser($user)->check('viewHorizon'))->toBeFalse()
        ->and(Gate::check('viewHorizon'))->toBeFalse();
});

test('horizon dashboard route is inaccessible without an admin session', function () {
    $this->get('/horizon')->assertStatus(403);
});
