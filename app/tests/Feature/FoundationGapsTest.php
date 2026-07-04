<?php

declare(strict_types=1);

use App\Filament\Auth\PanelLogin;
use App\Http\Middleware\VerifyResendSignature;
use App\Models\Company;
use App\Models\EmailSuppression;
use App\Models\User;
use App\Support\Mail\FlowFlexMailable;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Route;

/*
 * Phase-0 reconciliation sweep (2026-07-04): tests for spec Test Checklist
 * items that had implementations but no coverage.
 */

// ---- foundation.email-setup / bounce-webhook ------------------------------

test('the resend webhook route enforces signature verification and throttling', function () {
    $route = Route::getRoutes()->getByName('webhooks.resend');

    expect($route)->not->toBeNull()
        ->and($route->gatherMiddleware())
        ->toContain(VerifyResendSignature::class)
        ->toContain('throttle:60,1');
});

// ---- foundation.email-setup / branded-mailable (suppression list) ---------

class SuppressionProbeMailable extends FlowFlexMailable
{
    public function content(): Content
    {
        return new Content(htmlString: '<p>probe</p>');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Probe');
    }
}

test('mail to an undeliverable address is suppressed at send time', function () {
    $company = setCompany(Company::factory()->create());
    $user = User::factory()->for($company)->create(['email_deliverable' => false]);

    $mailable = (new SuppressionProbeMailable)->to($user->email);
    $result = $mailable->send(app('mail.manager'));

    expect($result)->toBeNull();
});

test('mail to an address on the suppression list is blocked even without a user account', function () {
    setCompany(Company::factory()->create());
    EmailSuppression::query()->create([
        'email' => 'external@bounced.nl',
        'reason' => 'complaint',
        'suppressed_at' => now(),
    ]);

    $mailable = (new SuppressionProbeMailable)->to('external@bounced.nl');
    $result = $mailable->send(app('mail.manager'));

    expect($result)->toBeNull();
});

test('mail to a deliverable address still goes out', function () {
    $company = setCompany(Company::factory()->create());
    $user = User::factory()->for($company)->create(['email_deliverable' => true]);

    $mailable = (new SuppressionProbeMailable)->to($user->email);
    $result = $mailable->send(app('mail.manager'));

    expect($result)->not->toBeNull();
});

// ---- foundation.filament-panels / app-panel-shell (setup wizard) ----------

test('setup-wizard middleware no-ops while the wizard route does not exist', function () {
    $company = Company::factory()->create(['setup_completed_at' => null]);
    $user = User::factory()->for($company)->create();

    // Soft dependency: until core.setup-wizard registers its page route the
    // middleware must degrade to nothing — /app stays reachable.
    $this->actingAs($user)->get('/app')->assertOk();
});

// ---- foundation.filament-panels / panel-auth (login validation) -----------

test('invalid credentials surface a form error and leave the session guest', function () {
    $user = User::factory()->for(Company::factory())->create(['password' => 'secret123']);

    Filament\Facades\Filament::setCurrentPanel('app');

    Livewire\Livewire::test(PanelLogin::class)
        ->set('data.email', $user->email)
        ->set('data.password', 'definitely-wrong')
        ->call('authenticate')
        ->assertHasErrors(['data.email']);

    expect(auth('web')->check())->toBeFalse();
});

test('login throttling engages after repeated failed attempts', function () {
    $user = User::factory()->for(Company::factory())->create(['password' => 'secret123']);

    Filament\Facades\Filament::setCurrentPanel('app');

    $test = Livewire\Livewire::test(PanelLogin::class)
        ->set('data.email', $user->email)
        ->set('data.password', 'definitely-wrong');

    foreach (range(1, 5) as $i) {
        $test->call('authenticate');
    }

    // 6th attempt: rate limiter fires a notification instead of validating
    $test->call('authenticate')->assertNotified();

    expect(auth('web')->check())->toBeFalse();
});

// ---- foundation.queue-workers / job-processing -----------------------------

test('the defaults supervisor declares the queue priority order', function () {
    expect(config('horizon.defaults.supervisor-1.queue'))->toBe([
        'domain-events', 'notifications', 'hr', 'finance', 'webhooks', 'exports', 'imports', 'default',
    ]);
});

// ---- foundation.queue-workers / scheduled-commands --------------------------

test('every scheduled command declares withoutOverlapping and onOneServer', function () {
    $events = app(Schedule::class)->events();

    expect($events)->not->toBeEmpty();

    foreach ($events as $event) {
        expect($event->withoutOverlapping)->toBeTrue()
            ->and($event->onOneServer)->toBeTrue();
    }
});
