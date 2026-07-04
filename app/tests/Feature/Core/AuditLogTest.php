<?php

declare(strict_types=1);

use App\Console\Commands\PruneAuditLogCommand;
use App\Filament\App\Resources\AuditLogResource\Pages\ListAuditLogs;
use App\Models\Activity;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\User;
use App\Support\Services\AuditLogger;
use App\Support\States\AuditedTransition;
use Database\Seeders\PermissionSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Tests\Support\TestItem;

beforeEach(function (): void {
    TestItem::migrate();
});

test('AuditLogger::log inserts one row with company_id from context', function () {
    $company = setCompany(Company::factory()->create());
    $user = User::factory()->for($company)->create();
    $item = TestItem::query()->create(['name' => 'Widget']);

    app(AuditLogger::class)->log('hr.updated', $item, $user, ['attributes' => ['name' => 'Widget']]);

    $row = Activity::query()->sole();
    expect($row->company_id)->toBe($company->id)
        ->and($row->causer_id)->toBe($user->id)
        ->and($row->subject_id)->toBe($item->id)
        ->and($row->event)->toBe('hr.updated');
});

test('company A audit rows are invisible to company B', function () {
    $companyA = setCompany(Company::factory()->create());
    $item = TestItem::query()->create(['name' => 'A-item']);
    app(AuditLogger::class)->log('hr.created', $item, null);

    setCompany(Company::factory()->create());

    expect(Activity::query()->count())->toBe(0);
});

test('a forged company_id in properties never wins over context', function () {
    $company = setCompany(Company::factory()->create());
    $other = Company::factory()->create();
    $item = TestItem::query()->create(['name' => 'Widget']);

    app(AuditLogger::class)->log('hr.updated', $item, null, ['company_id' => $other->id]);

    $row = Activity::query()->sole();
    expect($row->company_id)->toBe($company->id)
        ->and($row->properties->has('company_id'))->toBeFalse();
});

test('the audited transition base class writes a state-transition row with from and to', function () {
    setCompany(Company::factory()->create());
    $item = TestItem::query()->create(['name' => 'Widget']);

    $transition = new class($item) extends AuditedTransition
    {
        public function __construct(private TestItem $item) {}

        public function model(): Model
        {
            return $this->item;
        }

        public function fromState(): string
        {
            return 'draft';
        }

        public function toState(): string
        {
            return 'active';
        }

        protected function apply(): Model
        {
            return $this->item;
        }
    };

    $transition->handle();

    $row = Activity::query()->sole();
    expect($row->event)->toBe('state-transition')
        ->and($row->properties['from'])->toBe('draft')
        ->and($row->properties['to'])->toBe('active');
});

test('the prune command respects per-company retention and is idempotent', function () {
    $longRetention = Company::factory()->create(['audit_retention_days' => 3650]);
    $defaultRetention = Company::factory()->create(); // null -> 730 days

    setCompany($defaultRetention);
    $item = TestItem::query()->create(['name' => 'old']);
    app(AuditLogger::class)->log('hr.created', $item, null);
    app(AuditLogger::class)->log('hr.updated', $item, null);
    Activity::query()->first()->forceFill(['created_at' => now()->subDays(800)])->saveQuietly();

    setCompany($longRetention);
    $item2 = TestItem::query()->create(['name' => 'kept']);
    app(AuditLogger::class)->log('hr.created', $item2, null);
    Activity::query()->first()->forceFill(['created_at' => now()->subDays(800)])->saveQuietly();

    $this->artisan(PruneAuditLogCommand::class)->assertSuccessful();
    $this->artisan(PruneAuditLogCommand::class)->assertSuccessful(); // idempotent

    setCompany($defaultRetention);
    expect(Activity::query()->count())->toBe(1); // 800-day row pruned at 730

    setCompany($longRetention);
    expect(Activity::query()->count())->toBe(1); // 800-day row kept at 3650
});

test('the log browser renders read-only and filters narrow the table', function () {
    $company = setCompany(Company::factory()->create());
    $user = User::factory()->for($company)->create();

    $this->seed(PermissionSeeder::class);
    $role = Role::query()->create(['name' => 'auditor', 'guard_name' => 'web', 'company_id' => $company->id]);
    $role->givePermissionTo('core.audit.view-any');
    $user->assignRole($role);

    CompanyModuleSubscription::query()->create([
        'company_id' => $company->id,
        'module_key' => 'core.audit',
        'activated_at' => now(),
    ]);
    Cache::forget("company:{$company->id}:modules");

    $item = TestItem::query()->create(['name' => 'Widget']);
    app(AuditLogger::class)->log('hr.created', $item, $user);
    app(AuditLogger::class)->log('finance.created', $item, $user);

    $this->actingAs($user);
    Filament\Facades\Filament::setCurrentPanel('app');

    Livewire\Livewire::test(ListAuditLogs::class)
        ->assertSuccessful()
        ->assertCountTableRecords(2)
        ->filterTable('log_name', 'hr')
        ->assertCountTableRecords(1);
});
