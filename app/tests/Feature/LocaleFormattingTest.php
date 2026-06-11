<?php

declare(strict_types=1);

use App\Models\Company;
use App\Settings\CompanyLocaleSettings;
use App\Support\Services\LocaleFormatter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
});

it('formats dates per company setting and timezone', function () {
    $settings = app(CompanyLocaleSettings::class);
    $settings->timezone = 'Europe/Amsterdam';
    $settings->date_format = 'd-m-Y';
    $settings->save();

    $formatter = new LocaleFormatter(app(CompanyLocaleSettings::class)->refresh());

    // 23:30 UTC = next day 01:30 in Amsterdam (CEST).
    expect($formatter->date(Carbon::parse('2026-06-10 23:30:00', 'UTC')))->toBe('11-06-2026');
});

it('formats numbers with locale separators', function () {
    $settings = app(CompanyLocaleSettings::class);
    $settings->locale = 'nl';
    $settings->save();

    $formatter = new LocaleFormatter(app(CompanyLocaleSettings::class)->refresh());

    expect($formatter->number(1234.5))->toBe('1.234,50');
});

it('formats money per symbol position', function () {
    $settings = app(CompanyLocaleSettings::class);
    $settings->locale = 'en';
    $settings->currency = 'EUR';
    $settings->currency_position = 'before';
    $settings->save();

    $formatter = new LocaleFormatter(app(CompanyLocaleSettings::class)->refresh());

    expect($formatter->money(375050))->toBe('€3,750.50');
});

it('health endpoint responds with throttling applied', function () {
    $this->getJson('/api/health')->assertOk();
});
