<?php

declare(strict_types=1);

use App\Models\Company;
use App\Support\Mail\FlowFlexMailable;
use App\Support\Services\CompanyContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

/** Concrete test mailable. */
class BrandedTestMail extends FlowFlexMailable
{
    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Test');
    }

    public function content(): Content
    {
        return new Content(htmlString: "<p>{$this->branding()['companyName']}</p>");
    }

    /** Expose branding for assertions. */
    public function brandingData(): array
    {
        return $this->branding();
    }
}

it('queues FlowFlex mail on the notifications queue, never sync', function () {
    Mail::fake();

    Mail::to('someone@example.com')->send(new BrandedTestMail);

    Mail::assertQueued(BrandedTestMail::class, fn (BrandedTestMail $m) => $m->queue === 'notifications');
    Mail::assertNothingSent();
});

it('resolves company branding from context at render time', function () {
    $company = Company::factory()->create(['name' => 'Acme BV']);
    app(CompanyContext::class)->set($company);

    expect((new BrandedTestMail)->brandingData()['companyName'])->toBe('Acme BV');
});

it('falls back to app name without company context', function () {
    app(CompanyContext::class)->forget();

    expect((new BrandedTestMail)->brandingData()['companyName'])->toBe(config('app.name'));
});
