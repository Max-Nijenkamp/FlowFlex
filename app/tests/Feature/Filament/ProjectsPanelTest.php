<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\User;
use App\Support\Services\CompanyContext;

describe('Projects Panel', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user    = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);
    });

    it('redirects unauthenticated user to login', function () {
        $this->get('/projects')->assertRedirect('/projects/login');
    });

    it('shows dashboard to authenticated user', function () {
        $this->actingAs($this->user)
            ->get('/projects')
            ->assertOk();
    });

    it('projects login page loads', function () {
        $this->get('/projects/login')->assertOk();
    });

    it('projects panel is distinct from app and hr panels', function () {
        $this->get('/projects/login')->assertOk();
        $this->get('/app/login')->assertOk();
        $this->get('/hr/login')->assertOk();
    });
});
