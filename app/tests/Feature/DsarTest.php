<?php

declare(strict_types=1);

use App\Actions\CreateDsarRequestAction;
use App\Data\CreateDsarRequestData;
use App\Events\DSARRequestSubmitted;
use App\Jobs\ProcessAccessRequestJob;
use App\Jobs\ProcessErasureRequestJob;
use App\Models\Company;
use App\Models\DsarRequest;
use App\Models\User;
use App\Support\Privacy\PersonalDataRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake();
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);

    // Register the users table as PII for the exercise.
    app(PersonalDataRegistry::class)->register('core.rbac', [
        'users' => [
            'email_column' => 'email',
            'fields' => ['first_name', 'last_name', 'email'],
            'erasure' => 'anonymise',
        ],
    ]);
});

it('creates a request with a 30-day deadline and fires the event', function () {
    Event::fake([DSARRequestSubmitted::class]);

    $request = CreateDsarRequestAction::run(new CreateDsarRequestData('subject@example.com', 'access'));

    expect($request->due_at->isFuture())->toBeTrue()
        ->and((string) $request->status)->toBe('received');

    Event::assertDispatched(DSARRequestSubmitted::class, fn ($e) => $e->company_id === $this->company->id
        && $e->request_type === 'access');
});

it('access request exports the subject rows from registered tables', function () {
    $subject = User::factory()->forCompany($this->company)->create(['email' => 'subject@example.com']);
    User::factory()->forCompany($this->company)->create(); // unrelated
    $request = DsarRequest::factory()->forCompany($this->company)->create(['subject_email' => 'subject@example.com']);

    (new ProcessAccessRequestJob($request->id))->handle(app(PersonalDataRegistry::class));

    $fresh = $request->fresh();
    expect((string) $fresh->status)->toBe('completed')
        ->and($fresh->result_path)->not->toBeNull();

    $csv = Storage::get($fresh->result_path);
    expect($csv)->toContain('subject@example.com')
        ->and($csv)->not->toContain($subject->company->users()->where('email', '!=', 'subject@example.com')->first()->email);
});

it('erasure anonymises declared fields and keeps the row', function () {
    $subject = User::factory()->forCompany($this->company)->create(['email' => 'erase-me@example.com']);
    $request = DsarRequest::factory()->forCompany($this->company)->erasure()->create(['subject_email' => 'erase-me@example.com']);

    (new ProcessErasureRequestJob($request->id))->handle(app(PersonalDataRegistry::class));

    $fresh = $subject->fresh();
    expect($fresh)->not->toBeNull() // row kept
        ->and($fresh->first_name)->toBe('[erased]')
        ->and($fresh->email)->toEndWith('@erased.invalid')
        ->and((string) $request->fresh()->status)->toBe('completed');
});

it('keeps DSAR rows isolated between companies', function () {
    DsarRequest::factory()->forCompany($this->company)->create();

    $other = Company::factory()->create();
    $this->setCompany($other);

    expect(DsarRequest::count())->toBe(0);
});
