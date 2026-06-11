<?php

declare(strict_types=1);

use App\Models\Company;
use App\Settings\CompanyIdentitySettings;
use App\Settings\CompanyLocaleSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('reads defaults for a fresh company without seeded settings rows', function () {
    $company = Company::factory()->create();
    $this->setCompany($company);

    $locale = app(CompanyLocaleSettings::class);

    expect($locale->timezone)->toBe('Europe/Amsterdam')
        ->and($locale->currency)->toBe('EUR')
        ->and($locale->decimal_places)->toBe(2);
});

it('persists settings per company without leaking across tenants', function () {
    $a = Company::factory()->create();
    $b = Company::factory()->create();

    $this->setCompany($a);
    $settings = app(CompanyIdentitySettings::class);
    $settings->name = 'Company A Branding';
    $settings->primary_color = '#FF0000';
    $settings->save();

    // Fresh container resolution under company B — must see defaults, not A's values.
    $this->setCompany($b);
    app()->forgetInstance(CompanyIdentitySettings::class);
    $fresh = app(CompanyIdentitySettings::class)->refresh();

    expect($fresh->name)->not->toBe('Company A Branding')
        ->and($fresh->primary_color)->toBe('#38BDF8');

    // And company A still reads its own.
    $this->setCompany($a);
    app()->forgetInstance(CompanyIdentitySettings::class);
    $mine = app(CompanyIdentitySettings::class)->refresh();

    expect($mine->name)->toBe('Company A Branding');
});

it('updates an existing setting in place', function () {
    $company = Company::factory()->create();
    $this->setCompany($company);

    $locale = app(CompanyLocaleSettings::class);
    $locale->timezone = 'Europe/Berlin';
    $locale->save();

    $locale->timezone = 'Europe/London';
    $locale->save();

    app()->forgetInstance(CompanyLocaleSettings::class);
    expect(app(CompanyLocaleSettings::class)->refresh()->timezone)->toBe('Europe/London');
});
