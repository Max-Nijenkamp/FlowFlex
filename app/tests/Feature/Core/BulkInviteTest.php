<?php

declare(strict_types=1);

use App\Actions\BulkInviteAction;
use App\Models\Company;
use App\Models\UserInvitation;
use App\Support\Services\BuiltInRoles;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\Mail;

function bulkInviteCompany(): Company
{
    test()->seed(PermissionSeeder::class);
    $company = setCompany(Company::factory()->create());
    BuiltInRoles::ensure($company);

    return $company;
}

test('bulk invite sends one row at a time, skipping headers, bad addresses and duplicates', function () {
    Mail::fake();
    bulkInviteCompany();

    $result = BulkInviteAction::run(
        "email,role\nanna@company.nl\nbram@company.nl,manager\nnot-an-email\nanna@company.nl",
        'employee',
    );

    expect($result['sent'])->toBe(2)
        ->and($result['failures'])->toHaveCount(2)
        ->and($result['failures'][0])->toContain('not-an-email');

    expect(UserInvitation::query()->where('email', 'anna@company.nl')->value('role'))->toBe('employee')
        ->and(UserInvitation::query()->where('email', 'bram@company.nl')->value('role'))->toBe('manager');
});

test('a row naming an unknown role fails that row only', function () {
    Mail::fake();
    bulkInviteCompany();

    $result = BulkInviteAction::run("ok@company.nl\nnope@company.nl,ghost-role", 'employee');

    expect($result['sent'])->toBe(1)
        ->and($result['failures'])->toHaveCount(1)
        ->and($result['failures'][0])->toContain('nope@company.nl');
});
