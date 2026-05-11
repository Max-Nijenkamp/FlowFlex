<?php

declare(strict_types=1);

use App\Jobs\Middleware\WithCompanyContext;
use App\Models\Company;
use App\Support\Services\CompanyContext;

describe('WithCompanyContext Job Middleware', function () {
    it('sets company context during job execution', function () {
        $company = Company::factory()->create(['status' => 'active']);
        $context = app(CompanyContext::class);

        $contextIdDuringJob = null;

        $middleware = new WithCompanyContext($company->id);
        $middleware->handle(new \stdClass(), function () use ($context, &$contextIdDuringJob): void {
            $contextIdDuringJob = $context->currentId();
        });

        expect($contextIdDuringJob)->toBe($company->id);
    });

    it('clears company context after job completes', function () {
        $company = Company::factory()->create(['status' => 'active']);
        $context = app(CompanyContext::class);

        $middleware = new WithCompanyContext($company->id);
        $middleware->handle(new \stdClass(), function () use ($context): void {
            expect($context->hasCompany())->toBeTrue();
        });

        // after job: context should be cleared
        expect($context->hasCompany())->toBeFalse();
        expect($context->currentId())->toBeNull();
    });

    it('clears company context even when job throws', function () {
        $company = Company::factory()->create(['status' => 'active']);
        $context = app(CompanyContext::class);

        $middleware = new WithCompanyContext($company->id);

        try {
            $middleware->handle(new \stdClass(), function (): void {
                throw new \RuntimeException('Job failed');
            });
        } catch (\RuntimeException) {
            // expected
        }

        expect($context->hasCompany())->toBeFalse();
        expect($context->currentId())->toBeNull();
    });
});
