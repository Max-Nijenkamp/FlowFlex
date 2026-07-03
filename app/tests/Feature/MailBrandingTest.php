<?php

declare(strict_types=1);

use App\Models\Company;
use App\Support\Mail\FlowFlexMailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ProbeMailable extends FlowFlexMailable
{
    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Probe');
    }

    public function content(): Content
    {
        return new Content(htmlString: 'Hello from {{ $branding["name"] }}');
    }
}

test('mailable resolves the sending company branding and carries company_id', function () {
    $company = setCompany(Company::factory()->create(['name' => 'Acme BV']));

    $mailable = new ProbeMailable;

    expect($mailable->company_id)->toBe($company->id)
        ->and($mailable->branding()['name'])->toBe('Acme BV')
        ->and($mailable->branding()['primary_color'])->toBe('#38BDF8');
});

test('branding degrades to the platform default without a company context', function () {
    $mailable = new ProbeMailable;

    expect($mailable->branding()['name'])->toBe('FlowFlex');
});

test('every FlowFlex mailable queues on the notifications queue, never sync', function () {
    setCompany(Company::factory()->create());

    $mailable = new ProbeMailable;

    expect($mailable)->toBeInstanceOf(ShouldQueue::class)
        ->and($mailable->queue)->toBe('notifications');
});
