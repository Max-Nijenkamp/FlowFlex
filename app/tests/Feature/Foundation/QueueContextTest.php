<?php

declare(strict_types=1);

use App\Models\Company;
use App\Support\Jobs\Middleware\WithCompanyContext;
use App\Support\Services\CompanyContext;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(fn () => app(CompanyContext::class)->forget());

it('restores company context + permission team id inside a job', function () {
    $company = Company::factory()->create();

    $job = new class($company->id)
    {
        public function __construct(public string $company_id) {}
    };

    $ran = false;
    (new WithCompanyContext)->handle($job, function () use (&$ran, $company) {
        $ran = true;
        expect(app(CompanyContext::class)->current()->id)->toBe($company->id)
            ->and(getPermissionsTeamId())->toBe($company->id);
    });

    expect($ran)->toBeTrue();
});

it('reads company_id from a wrapped event', function () {
    $company = Company::factory()->create();

    $event = new class($company->id)
    {
        public function __construct(public string $company_id) {}
    };
    $job = new class($event)
    {
        public function __construct(public object $event) {}
    };

    (new WithCompanyContext)->handle($job, function () use ($company) {
        expect(app(CompanyContext::class)->current()->id)->toBe($company->id);
    });
});

it('passes through jobs without a company_id without crashing', function () {
    $job = new class
    {
        public string $unrelated = 'x';
    };

    $ran = false;
    (new WithCompanyContext)->handle($job, function () use (&$ran) {
        $ran = true;
        expect(app(CompanyContext::class)->has())->toBeFalse();
    });

    expect($ran)->toBeTrue();
});

it('registers the failed-job prune on the scheduler', function () {
    $schedule = app(Schedule::class);
    $commands = collect($schedule->events())->map(fn ($e) => $e->command);

    expect($commands->contains(fn ($c) => str_contains((string) $c, 'queue:prune-failed')))->toBeTrue();
});
