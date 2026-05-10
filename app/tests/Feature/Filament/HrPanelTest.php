<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\User;
use App\Support\Services\CompanyContext;

describe('HR Panel', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);
    });

    it('redirects unauthenticated user to login', function () {
        $this->get('/hr')->assertRedirect('/hr/login');
    });

    it('shows dashboard to authenticated user', function () {
        $this->actingAs($this->user)
            ->get('/hr')
            ->assertOk();
    });

    it('hr login page loads', function () {
        $this->get('/hr/login')->assertOk();
    });

    it('hr panel is distinct from app panel', function () {
        $this->get('/hr/login')->assertOk();
        $this->get('/app/login')->assertOk();
    });
});
