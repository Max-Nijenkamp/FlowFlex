<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\Foundation\ActivityLog;
use App\Models\User;
use App\Services\Core\AuditLogger;
use App\Support\Services\CompanyContext;

describe('Audit Log', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);
        $this->actingAs($this->user, 'web');
    });

    it('records activity when a user is updated', function () {
        $this->user->update(['first_name' => 'UpdatedName']);

        expect(ActivityLog::where('subject_type', User::class)
            ->where('subject_id', $this->user->id)
            ->where('event', 'updated')
            ->exists()
        )->toBeTrue();
    });

    it('records activity when a user is created', function () {
        $newUser = User::factory()->create(['company_id' => $this->company->id]);

        expect(ActivityLog::where('subject_type', User::class)
            ->where('subject_id', $newUser->id)
            ->where('event', 'created')
            ->exists()
        )->toBeTrue();
    });

    it('records activity when a company is updated', function () {
        $this->company->update(['name' => 'New Company Name']);

        expect(ActivityLog::where('subject_type', Company::class)
            ->where('subject_id', $this->company->id)
            ->where('event', 'updated')
            ->exists()
        )->toBeTrue();
    });

    it('AuditLogger can log custom events', function () {
        $logger = app(AuditLogger::class);
        $logger->log('User exported data', 'export', $this->user, ['rows' => 500]);

        expect(ActivityLog::where('description', 'User exported data')
            ->where('log_name', 'audit')
            ->exists()
        )->toBeTrue();
    });

    it('audit log does not have updated_at column', function () {
        expect(\Illuminate\Support\Facades\Schema::hasColumn('activity_log', 'updated_at'))->toBeFalse();
    });
});
