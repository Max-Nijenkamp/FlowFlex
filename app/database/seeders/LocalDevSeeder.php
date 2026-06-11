<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Contracts\BillingServiceInterface;
use App\Models\Admin;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\CRM\Contact;
use App\Models\CRM\Deal;
use App\Models\CRM\PipelineStage;
use App\Models\Finance\Customer;
use App\Models\Finance\DunningRule;
use App\Models\Finance\ExchangeRate;
use App\Models\Finance\ExpenseCategory;
use App\Models\Finance\FixedAsset;
use App\Models\Finance\Supplier;
use App\Models\Finance\TaxRate;
use App\Models\HR\Department;
use App\Models\HR\Employee;
use App\Models\HR\LeaveBalance;
use App\Models\HR\LeaveType;
use App\Models\User;
use App\Support\Services\CompanyContext;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Local/demo data: FlowFlex staff admin, the "FlowFlex Demo" tenant + owner,
 * and a handful of demo users. Refuses to run in production.
 */
class LocalDevSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('LocalDevSeeder must never run in production.');
        }

        // FlowFlex staff console login.
        Admin::firstOrCreate(
            ['email' => 'admin@flowflex.nl'],
            ['name' => 'FlowFlex Admin', 'password' => Hash::make('password'), 'role' => 'super_admin'],
        );

        // Quick-test staff login (founder request 2026-06-11).
        Admin::firstOrCreate(
            ['email' => 'test@test.nl'],
            ['name' => 'Test Admin', 'password' => Hash::make('test1234'), 'role' => 'super_admin'],
        );

        // Demo tenant.
        $company = Company::firstOrCreate(
            ['slug' => 'flowflex-demo'],
            ['name' => 'FlowFlex Demo', 'subscription_status' => 'active', 'setup_completed_at' => now()],
        );

        app(CompanyContext::class)->set($company);
        setPermissionsTeamId($company->id);

        // Owner role gets every permission; stays in sync as new permissions are seeded.
        $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web', 'team_id' => $company->id]);
        $owner->syncPermissions(Permission::where('guard_name', 'web')->get());

        $ownerUser = User::firstOrCreate(
            ['company_id' => $company->id, 'email' => 'demo@flowflex.nl'],
            ['first_name' => 'Demo', 'last_name' => 'Owner', 'password' => Hash::make('password'), 'email_verified_at' => now()],
        );
        $ownerUser->assignRole($owner);

        // Quick-test tenant login — every permission, every module (founder request 2026-06-11).
        $testUser = User::firstOrCreate(
            ['company_id' => $company->id, 'email' => 'test@test.nl'],
            ['first_name' => 'Test', 'last_name' => 'User', 'password' => Hash::make('test1234'), 'email_verified_at' => now()],
        );
        $testUser->assignRole($owner);

        // Free core modules active for the demo company.
        app(BillingServiceInterface::class)->seedFreeCoreModules($company->id);

        // A few extra demo users.
        User::factory()->forCompany($company)->count(5)->create();

        // All MVP modules active for the demo company.
        foreach (array_keys(config('flowflex.modules', [])) as $moduleKey) {
            CompanyModuleSubscription::firstOrCreate(
                ['company_id' => $company->id, 'module_key' => $moduleKey, 'deactivated_at' => null],
                ['activated_at' => now()],
            );
        }
        cache()->forget("company:{$company->id}:modules");

        // --- HR demo data ---
        $engineering = Department::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Engineering'],
        );
        $employees = collect([
            ['Sanne', 'de Vries', 'sanne@flowflex-demo.nl', 'Engineering Manager'],
            ['Tim', 'Bakker', 'tim@flowflex-demo.nl', 'Software Engineer'],
            ['Lisa', 'Visser', 'lisa@flowflex-demo.nl', 'Designer'],
        ])->map(fn (array $row, int $i) => Employee::firstOrCreate(
            ['company_id' => $company->id, 'email' => $row[2]],
            [
                'employee_number' => (string) ($i + 1),
                'first_name' => $row[0],
                'last_name' => $row[1],
                'job_title' => $row[3],
                'hire_date' => now()->subMonths(6 + $i),
                'employment_type' => 'full-time',
                'department_id' => $engineering->id,
            ],
        ));
        $annual = LeaveType::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Annual Leave'],
            ['accrual_days_per_year' => 25, 'carry_over_days' => 5],
        );
        foreach ($employees as $employee) {
            LeaveBalance::firstOrCreate(
                ['company_id' => $company->id, 'employee_id' => $employee->id, 'leave_type_id' => $annual->id, 'year' => now()->year],
                ['allocated_days' => 25],
            );
        }

        // --- Finance demo data ---
        $customer = Customer::firstOrCreate(
            ['company_id' => $company->id, 'email' => 'billing@acme-client.nl'],
            ['name' => 'Acme Client BV', 'payment_terms_days' => 14],
        );
        ExpenseCategory::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Travel'],
            ['limit_per_transaction_cents' => 25000],
        );

        // --- Finance v1 demo data (suppliers, tax, budgets, assets, fx) ---
        $supplier = Supplier::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Office Supplies BV'],
            ['email' => 'sales@officesupplies.nl', 'iban' => 'NL91ABNA0417164300', 'payment_terms_days' => 30],
        );
        TaxRate::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'NL High 21%'],
            ['rate_basis_points' => 2100, 'type' => 'vat', 'jurisdiction' => 'NL'],
        );
        TaxRate::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'NL Low 9%'],
            ['rate_basis_points' => 900, 'type' => 'vat', 'jurisdiction' => 'NL'],
        );
        DunningRule::firstOrCreate(
            ['company_id' => $company->id, 'escalation_level' => 1],
            ['aging_bucket' => '1-30', 'days_overdue' => 7, 'email_template' => 'dunning-friendly'],
        );
        if (! ExchangeRate::query()
            ->where('from_currency', 'USD')->where('to_currency', 'EUR')
            ->whereDate('effective_date', now()->toDateString())->exists()) {
            ExchangeRate::create([
                'company_id' => $company->id, 'from_currency' => 'USD', 'to_currency' => 'EUR',
                'effective_date' => now()->toDateString(), 'rate' => '0.92000000',
            ]);
        }
        FixedAsset::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Laptop fleet'],
            [
                'category' => 'it-equipment', 'cost_cents' => 1800000, 'purchase_date' => now()->subMonths(6)->toDateString(),
                'useful_life_months' => 36, 'method' => 'straight-line', 'salvage_cents' => 180000,
            ],
        );

        // --- CRM demo data ---
        $stages = collect([['Lead', 1, 10], ['Qualified', 2, 30], ['Proposal', 3, 60], ['Negotiation', 4, 80]])
            ->map(fn (array $row) => PipelineStage::firstOrCreate(
                ['company_id' => $company->id, 'name' => $row[0]],
                ['order' => $row[1], 'probability_default' => $row[2]],
            ));
        $contact = Contact::firstOrCreate(
            ['company_id' => $company->id, 'email' => 'jan@acme-client.nl'],
            ['first_name' => 'Jan', 'last_name' => 'Smit', 'lifecycle_stage' => 'opportunity', 'owner_id' => $ownerUser->id],
        );
        Deal::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Acme Platform Licence'],
            [
                'stage_id' => $stages[2]->id,
                'contact_id' => $contact->id,
                'owner_id' => $ownerUser->id,
                'value_cents' => 1200000,
                'probability' => 60,
                'expected_close_date' => now()->addMonth(),
                'stage_entered_at' => now(),
            ],
        );
    }
}
