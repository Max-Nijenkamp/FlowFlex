<?php

declare(strict_types=1);

use App\Contracts\CRM\QuoteServiceInterface;
use App\Data\CRM\CreateQuoteData;
use App\Models\Company;
use App\Models\CRM\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
    $this->actingAs(User::factory()->forCompany($this->company)->create(), 'web');
    $this->quotes = app(QuoteServiceInterface::class);
});

it('creates a quote with brick/money line totals', function () {
    $quote = $this->quotes->create(new CreateQuoteData(lines: [
        ['description' => 'Implementation', 'quantity' => 8, 'unit_price_cents' => 15000],
        ['description' => 'Licence', 'quantity' => 1, 'unit_price_cents' => 90000],
    ]));

    expect($quote->total_cents)->toBe(210000)
        ->and($quote->lines)->toHaveCount(2)
        ->and($quote->status)->toBe('draft');
});

it('send assigns number + single-use token; public page renders; accept consumes token', function () {
    $quote = $this->quotes->create(new CreateQuoteData(lines: [
        ['description' => 'Work', 'quantity' => 1, 'unit_price_cents' => 50000],
    ]));
    $quote = $this->quotes->send($quote->id);

    expect($quote->quote_number)->toBe('Q-2026-001')
        ->and($quote->accept_token)->not->toBeNull();

    // Public surface — no session needed.
    auth('web')->logout();
    $this->get("/quotes/accept/{$quote->accept_token}")
        ->assertOk()
        ->assertSee('Q-2026-001');

    $this->post("/quotes/accept/{$quote->accept_token}")->assertOk();

    $fresh = $quote->fresh();
    expect($fresh->status)->toBe('accepted')
        ->and($fresh->accepted_at)->not->toBeNull()
        ->and($fresh->accept_token)->toBeNull(); // consumed

    // Token single-use: second accept 404s.
    $this->post("/quotes/accept/{$quote->accept_token}")->assertNotFound();
});

it('rejects expired quotes at the public surface', function () {
    $quote = $this->quotes->create(new CreateQuoteData(
        lines: [['description' => 'Old', 'quantity' => 1, 'unit_price_cents' => 1000]],
    ));
    $quote = $this->quotes->send($quote->id);
    $quote->forceFill(['valid_until' => now()->subDay()])->save();

    auth('web')->logout();
    $this->post("/quotes/accept/{$quote->accept_token}")->assertNotFound();
});

it('keeps quotes isolated between companies', function () {
    $this->quotes->create(new CreateQuoteData(lines: [
        ['description' => 'Mine', 'quantity' => 1, 'unit_price_cents' => 1000],
    ]));

    $this->setCompany(Company::factory()->create());
    expect(Quote::count())->toBe(0);
});
