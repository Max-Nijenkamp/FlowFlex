<?php

declare(strict_types=1);

use App\Actions\CRM\ConvertLeadAction;
use App\Models\Company;
use App\Models\CRM\Contact;
use App\Models\CRM\Deal;
use App\Models\CRM\Lead;
use App\Models\CRM\Pipeline;
use App\Models\CRM\PipelineStage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
    $this->user = User::factory()->forCompany($this->company)->create();
    $this->actingAs($this->user, 'web');

    $this->pipeline = Pipeline::create([
        'company_id' => $this->company->id, 'name' => 'Sales', 'is_default' => true, 'order' => 0,
    ]);
    $this->stage = PipelineStage::create([
        'company_id' => $this->company->id, 'pipeline_id' => $this->pipeline->id,
        'name' => 'Lead', 'order' => 0, 'probability_default' => 10,
    ]);
});

it('converts a lead into a deal in the default pipeline first stage', function () {
    $lead = Lead::factory()->forCompany($this->company)->create([
        'name' => 'Pieter Hendriks',
        'company_name' => 'Hendriks Transport',
        'email' => 'pieter@hendriks.test',
        'estimated_value_cents' => 250_000,
        'owner_id' => $this->user->id,
        'status' => 'qualified',
    ]);

    $deal = ConvertLeadAction::run($lead);

    expect($deal)->toBeInstanceOf(Deal::class)
        ->and($deal->name)->toBe('Hendriks Transport')
        ->and($deal->stage_id)->toBe($this->stage->id)
        ->and($deal->value_cents)->toBe(250_000)
        ->and($deal->probability)->toBe(10.0);

    $lead->refresh();
    expect($lead->status)->toBe('converted')
        ->and($lead->converted_deal_id)->toBe($deal->id)
        ->and($lead->converted_at)->not->toBeNull();
});

it('creates a contact from the lead email on convert', function () {
    $lead = Lead::factory()->forCompany($this->company)->create([
        'name' => 'Sofie Maes', 'email' => 'sofie@maes.test', 'owner_id' => $this->user->id,
    ]);

    $deal = ConvertLeadAction::run($lead);

    expect($deal->contact_id)->not->toBeNull();
    expect(Contact::where('email', 'sofie@maes.test')->exists())->toBeTrue();
});

it('refuses to convert an already-converted lead', function () {
    $lead = Lead::factory()->forCompany($this->company)->create(['owner_id' => $this->user->id]);
    ConvertLeadAction::run($lead);

    ConvertLeadAction::run($lead->refresh());
})->throws(ValidationException::class);

it('scopes leads to the company', function () {
    $other = Company::factory()->create();
    Lead::factory()->forCompany($other)->create();
    Lead::factory()->forCompany($this->company)->count(2)->create();

    expect(Lead::count())->toBe(2);
});
