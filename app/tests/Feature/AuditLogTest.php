<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Company;
use App\Models\User;
use App\Support\Services\AuditLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
    $this->user = User::factory()->forCompany($this->company)->create();
});

it('writes a ULID log row with company_id set from context', function () {
    AuditLogger::log('hr.employee.updated', $this->user, $this->user, ['field' => 'title']);

    $row = Activity::query()->firstOrFail();

    expect(Str::isUlid($row->id))->toBeTrue()
        ->and($row->company_id)->toBe($this->company->id)
        ->and($row->log_name)->toBe('hr')
        ->and($row->causer_id)->toBe($this->user->id);
});

it('redacts PII values against the denylist, recursively', function () {
    AuditLogger::log('hr.employee.updated', $this->user, $this->user, [
        'salary' => 95000,
        'changes' => ['iban' => 'NL00BANK0123456789', 'title' => 'Manager'],
    ]);

    $properties = Activity::query()->firstOrFail()->properties;

    expect($properties['salary'])->toBe('[redacted]')
        ->and($properties['changes']['iban'])->toBe('[redacted]')
        ->and($properties['changes']['title'])->toBe('Manager');
});

it('keeps audit rows isolated between companies', function () {
    AuditLogger::log('core.settings.updated', null, $this->user);

    $other = Company::factory()->create();
    $this->setCompany($other);

    expect(Activity::count())->toBe(0);
});

it('prunes rows older than the retention period only', function () {
    AuditLogger::log('core.settings.updated', null, $this->user);
    Activity::query()->update(['created_at' => now()->subMonths(30)]);
    AuditLogger::log('core.settings.updated', null, $this->user);

    $this->artisan('flowflex:prune-audit-log', ['--months' => 24])->assertSuccessful();

    expect(Activity::count())->toBe(1);
});
