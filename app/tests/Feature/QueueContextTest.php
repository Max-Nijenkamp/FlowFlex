<?php

declare(strict_types=1);

use App\Models\Company;
use App\Support\Jobs\Middleware\WithCompanyContext;
use App\Support\Services\CompanyContext;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ContextProbeJob implements ShouldQueue
{
    use Queueable;

    public static ?string $seenCompanyId = null;

    public function __construct(public readonly string $company_id) {}

    public function middleware(): array
    {
        return [new WithCompanyContext];
    }

    public function handle(): void
    {
        self::$seenCompanyId = app(CompanyContext::class)->currentId();
    }
}

test('WithCompanyContext restores the right company inside a queued job', function () {
    $company = Company::factory()->create();
    ContextProbeJob::$seenCompanyId = null;

    // Simulate the worker boundary: nothing set before the job runs.
    app(CompanyContext::class)->forget();

    dispatch_sync(new ContextProbeJob($company->id));

    expect(ContextProbeJob::$seenCompanyId)->toBe($company->id);
});

class TenantlessProbeJob implements ShouldQueue
{
    use Queueable;

    public static ?string $seen = 'unset';

    public function middleware(): array
    {
        return [new WithCompanyContext];
    }

    public function handle(): void
    {
        self::$seen = app(CompanyContext::class)->currentId();
    }
}

test('a job without company_id leaves the context empty (null-tenant guard)', function () {
    TenantlessProbeJob::$seen = 'unset';
    app(CompanyContext::class)->forget();

    dispatch_sync(new TenantlessProbeJob);

    expect(TenantlessProbeJob::$seen)->toBeNull();
});
