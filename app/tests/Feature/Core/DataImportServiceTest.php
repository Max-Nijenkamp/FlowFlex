<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\Core\ImportJob;
use App\Models\Core\ImportJobRow;
use App\Models\User;
use App\Services\Core\DataImportService;
use App\Support\Services\CompanyContext;

describe('Data Import Service', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);
        $this->actingAs($this->user, 'web');
        $this->service = app(DataImportService::class);
    });

    it('creates an import job', function () {
        $job = $this->service->createJob('users', '/tmp/users.csv');

        expect($job)->toBeInstanceOf(ImportJob::class);
        expect($job->company_id)->toBe($this->company->id);
        expect($job->entity_type)->toBe('users');
        expect($job->status)->toBe('pending');
        expect($job->duplicate_strategy)->toBe('skip');
    });

    it('stores rows for a job', function () {
        $job = $this->service->createJob('contacts', '/tmp/contacts.csv');

        $rows = [
            ['name' => 'Alice', 'email' => 'alice@example.com'],
            ['name' => 'Bob', 'email' => 'bob@example.com'],
            ['name' => 'Carol', 'email' => 'carol@example.com'],
        ];

        $this->service->parseAndStoreRows($job, $rows);

        $job->refresh();
        expect($job->total_rows)->toBe(3);
        expect($job->status)->toBe('mapping');
        expect(ImportJobRow::where('import_job_id', $job->id)->count())->toBe(3);
    });

    it('import job has company scope isolation', function () {
        $otherCompany = Company::factory()->create(['status' => 'active']);
        $job1 = $this->service->createJob('users', '/tmp/a.csv');

        app(CompanyContext::class)->set($otherCompany);
        $job2 = $this->service->createJob('users', '/tmp/b.csv');

        app(CompanyContext::class)->set($this->company);

        // Use withoutGlobalScopes to verify cross-company isolation in DB
        expect(ImportJob::withoutGlobalScopes()->where('company_id', $this->company->id)->count())->toBe(1);
        expect(ImportJob::withoutGlobalScopes()->where('company_id', $otherCompany->id)->count())->toBe(1);
    });

    it('job rollback updates status', function () {
        $job = $this->service->createJob('users', '/tmp/a.csv');
        $job->update(['status' => 'done']);

        $this->service->rollback($job);
        $job->refresh();

        expect($job->status)->toBe('rolled_back');
    });
});
