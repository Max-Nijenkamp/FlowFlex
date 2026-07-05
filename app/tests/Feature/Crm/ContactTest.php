<?php

declare(strict_types=1);

use App\Contracts\Crm\ContactServiceInterface;
use App\Data\Crm\CreateContactData;
use App\Models\Company;
use App\Models\Crm\Account;
use App\Models\Crm\Activity;
use App\Models\Crm\Contact;
use App\Models\Crm\ContactAccount;
use App\Models\Crm\Deal;
use App\Models\Crm\Pipeline;
use App\Models\Crm\PipelineStage;
use App\Models\User;
use App\Support\Services\BuiltInRoles;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Validation\ValidationException;

function crmCompany(): array
{
    test()->seed(PermissionSeeder::class);
    test()->seed(ModuleCatalogSeeder::class);

    $company = setCompany(Company::factory()->create(['currency' => 'EUR']));
    BuiltInRoles::ensure($company);

    $owner = User::factory()->for($company)->create();
    $owner->assignRole('owner');
    test()->actingAs($owner);

    return [$company, $owner];
}

test('tenant isolation: company A contacts are invisible to company B', function () {
    [$companyA, $ownerA] = crmCompany();
    Contact::factory()->create(['company_id' => $companyA->id, 'owner_id' => $ownerA->id]);

    crmCompany(); // switches context to B

    expect(Contact::query()->count())->toBe(0);
});

test('duplicate email per company rejected; same email across companies allowed', function () {
    [$companyA, $ownerA] = crmCompany();
    $service = app(ContactServiceInterface::class);

    $service->create(new CreateContactData(firstName: 'Anna', lastName: 'Aa', email: 'a@x.nl'));

    expect(fn () => $service->create(new CreateContactData(firstName: 'Anna2', lastName: 'Bb', email: 'a@x.nl')))
        ->toThrow(ValidationException::class);

    crmCompany(); // company B
    $contact = app(ContactServiceInterface::class)->create(new CreateContactData(firstName: 'Ben', lastName: 'Cc', email: 'a@x.nl'));
    expect($contact->email)->toBe('a@x.nl');
});

test('findOrCreateByEmail is idempotent', function () {
    crmCompany();
    $service = app(ContactServiceInterface::class);

    $first = $service->findOrCreateByEmail('lead@site.nl', ['source' => 'form']);
    $second = $service->findOrCreateByEmail('lead@site.nl');

    expect($second->id)->toBe($first->id)
        ->and(Contact::query()->count())->toBe(1);
});

test('lifecycle stage moves accept any known stage and reject unknown ones', function () {
    [, $owner] = crmCompany();
    $contact = Contact::factory()->create(['company_id' => $owner->company_id, 'owner_id' => $owner->id]);
    $service = app(ContactServiceInterface::class);

    $service->moveLifecycleStage($contact->id, 'customer');
    expect($contact->fresh()->lifecycle_stage)->toBe('customer');

    // any-direction move allowed — no state machine
    $service->moveLifecycleStage($contact->id, 'lead');
    expect($contact->fresh()->lifecycle_stage)->toBe('lead');

    expect(fn () => $service->moveLifecycleStage($contact->id, 'galactic'))
        ->toThrow(InvalidArgumentException::class);
});

test('merge reassigns activities, deals and account links, backfills blanks, soft-deletes and audits', function () {
    [$company, $owner] = crmCompany();

    $keep = Contact::factory()->create(['company_id' => $company->id, 'owner_id' => $owner->id, 'email' => null, 'phone' => null]);
    $merge = Contact::factory()->create(['company_id' => $company->id, 'owner_id' => $owner->id, 'email' => 'dup@x.nl', 'phone' => '+31612345678']);

    $account = Account::factory()->create(['company_id' => $company->id, 'owner_id' => $owner->id]);
    ContactAccount::query()->create([
        'company_id' => $company->id, 'contact_id' => $merge->id, 'account_id' => $account->id,
    ]);

    $pipeline = Pipeline::factory()->create(['company_id' => $company->id]);
    $stage = PipelineStage::factory()->create(['company_id' => $company->id, 'pipeline_id' => $pipeline->id]);
    $deal = Deal::factory()->create([
        'company_id' => $company->id, 'owner_id' => $owner->id,
        'stage_id' => $stage->id, 'contact_id' => $merge->id,
    ]);
    $activity = Activity::factory()->create([
        'company_id' => $company->id, 'owner_id' => $owner->id, 'contact_id' => $merge->id,
    ]);

    app(ContactServiceInterface::class)->merge($keep->id, $merge->id);

    expect($deal->fresh()->contact_id)->toBe($keep->id)
        ->and($activity->fresh()->contact_id)->toBe($keep->id)
        ->and(ContactAccount::query()->where('contact_id', $keep->id)->exists())->toBeTrue()
        ->and(Contact::query()->find($merge->id))->toBeNull()
        ->and($keep->fresh()->phone)->toBe('+31612345678')
        ->and(App\Models\Activity::query()->where('event', 'crm.contact-merged')->exists())->toBeTrue();
});

test('merging a contact into itself is rejected', function () {
    [, $owner] = crmCompany();
    $contact = Contact::factory()->create(['company_id' => $owner->company_id, 'owner_id' => $owner->id]);

    expect(fn () => app(ContactServiceInterface::class)->merge($contact->id, $contact->id))
        ->toThrow(InvalidArgumentException::class);
});
