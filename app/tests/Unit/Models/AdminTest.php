<?php

declare(strict_types=1);

use App\Models\Admin;

describe('Admin Model', function () {
    it('isSuperAdmin returns true for super_admin role', function () {
        $admin       = new Admin(['role' => 'super_admin']);
        expect($admin->isSuperAdmin())->toBeTrue();
    });

    it('isSuperAdmin returns false for non-super_admin role', function () {
        $admin = new Admin(['role' => 'admin']);
        expect($admin->isSuperAdmin())->toBeFalse();
    });

    it('canImpersonate returns true for super_admin', function () {
        $admin = new Admin(['role' => 'super_admin']);
        expect($admin->canImpersonate())->toBeTrue();
    });

    it('canImpersonate returns true for support', function () {
        $admin = new Admin(['role' => 'support']);
        expect($admin->canImpersonate())->toBeTrue();
    });

    it('canImpersonate returns false for regular admin', function () {
        $admin = new Admin(['role' => 'admin']);
        expect($admin->canImpersonate())->toBeFalse();
    });
});
