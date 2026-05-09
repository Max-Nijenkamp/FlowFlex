<?php

declare(strict_types=1);

use App\Models\User;

describe('User Model', function () {
    it('getNameAttribute returns full name', function () {
        $user = new User(['first_name' => 'John', 'last_name' => 'Doe']);
        expect($user->name)->toBe('John Doe');
    });

    it('isActive returns true for active status', function () {
        $user = new User(['status' => 'active']);
        expect($user->isActive())->toBeTrue();
    });

    it('isActive returns false for other statuses', function () {
        expect((new User(['status' => 'invited']))->isActive())->toBeFalse();
        expect((new User(['status' => 'deactivated']))->isActive())->toBeFalse();
    });

    it('isInvited returns true for invited status', function () {
        $user = new User(['status' => 'invited']);
        expect($user->isInvited())->toBeTrue();
    });

    it('isDeactivated returns true for deactivated status', function () {
        $user = new User(['status' => 'deactivated']);
        expect($user->isDeactivated())->toBeTrue();
    });
});
