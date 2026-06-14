<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Contracts\BillingServiceInterface;
use App\Models\Admin;
use App\Models\BillingInvoice;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\CRM\Account;
use App\Models\CRM\Contact;
use App\Models\CRM\Deal;
use App\Models\CRM\Lead;
use App\Models\CRM\Pipeline;
use App\Models\CRM\PipelineStage;
use App\Models\Finance\Customer;
use App\Models\Finance\DunningRule;
use App\Models\Finance\ExchangeRate;
use App\Models\Finance\ExpenseCategory;
use App\Models\Finance\FixedAsset;
use App\Models\Finance\Supplier;
use App\Models\Finance\TaxRate;
use App\Models\HR\Applicant;
use App\Models\HR\Department;
use App\Models\HR\Employee;
use App\Models\HR\JobRequisition;
use App\Models\HR\LeaveBalance;
use App\Models\HR\LeaveRequest;
use App\Models\HR\LeaveType;
use App\Models\User;
use App\States\BillingInvoice\Paid;
use App\Support\Services\CompanyContext;
use Carbon\CarbonImmutable;
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

        // --- Staff console demo data: billing history for the dashboard ---
        $billing = app(BillingServiceInterface::class);
        foreach ([2, 1, 0] as $monthsAgo) {
            $invoiceData = $billing->generateMonthlyInvoice(
                $company->id,
                CarbonImmutable::now()->subMonths($monthsAgo),
            );

            // Older invoices paid, current month left open.
            if ($monthsAgo > 0) {
                $invoice = BillingInvoice::withoutGlobalScopes()->find($invoiceData->id);
                if ($invoice !== null && ! $invoice->status->equals(Paid::class)) {
                    $invoice->status->transitionTo(Paid::class);
                    $invoice->forceFill(['paid_at' => now()->subMonths($monthsAgo)->endOfMonth()])->save();
                }
            }
        }

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
        // Manager chain + a second department → real org-chart depth.
        $sales = Department::firstOrCreate(['company_id' => $company->id, 'name' => 'Sales']);
        $extra = collect([
            ['Noor', 'Hendriks', 'noor@flowflex-demo.nl', 'Head of Sales', $sales->id, 4],
            ['Bram', 'Peters', 'bram@flowflex-demo.nl', 'Account Executive', $sales->id, 9],
            ['Yara', 'Kuipers', 'yara@flowflex-demo.nl', 'SDR', $sales->id, 14],
            ['Sven', 'de Groot', 'sven@flowflex-demo.nl', 'Backend Engineer', $engineering->id, 2],
            ['Iris', 'Smits', 'iris@flowflex-demo.nl', 'QA Engineer', $engineering->id, 11],
        ])->map(fn (array $row, int $i) => Employee::firstOrCreate(
            ['company_id' => $company->id, 'email' => $row[2]],
            [
                'employee_number' => (string) ($i + 10),
                'first_name' => $row[0],
                'last_name' => $row[1],
                'job_title' => $row[3],
                'hire_date' => now()->subMonths($row[5]),
                'employment_type' => 'full-time',
                'department_id' => $row[4],
            ],
        ));

        $sanne = $employees[0];   // Engineering Manager
        $noor = $extra[0];        // Head of Sales
        Employee::whereIn('id', [$employees[1]->id, $employees[2]->id, $extra[3]->id, $extra[4]->id])
            ->update(['manager_id' => $sanne->id]);
        Employee::whereIn('id', [$extra[1]->id, $extra[2]->id])
            ->update(['manager_id' => $noor->id]);

        $annual = LeaveType::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Annual Leave'],
            ['accrual_days_per_year' => 25, 'carry_over_days' => 5],
        );

        // Leave activity for the dashboard: pending + approved-covering-today.
        LeaveRequest::firstOrCreate(
            ['company_id' => $company->id, 'employee_id' => $employees[1]->id, 'start_date' => now()->addWeek()->toDateString()],
            [
                'leave_type_id' => $annual->id, 'end_date' => now()->addWeek()->addDays(4)->toDateString(),
                'days_requested' => 5, 'status' => 'submitted', 'note' => 'Family trip',
            ],
        );
        LeaveRequest::firstOrCreate(
            ['company_id' => $company->id, 'employee_id' => $extra[1]->id, 'start_date' => now()->subDay()->toDateString()],
            [
                'leave_type_id' => $annual->id, 'end_date' => now()->addDays(2)->toDateString(),
                'days_requested' => 4, 'status' => 'approved', 'approved_at' => now()->subDays(3),
            ],
        );

        // Recruiting: an open role with applicants.
        $req = JobRequisition::firstOrCreate(
            ['company_id' => $company->id, 'slug' => 'senior-frontend-engineer'],
            [
                'title' => 'Senior Frontend Engineer', 'employment_type' => 'full-time',
                'status' => 'open', 'open_date' => now()->subWeeks(3)->toDateString(),
                'headcount' => 1, 'department_id' => $engineering->id,
                'description' => 'Vue 3 + TypeScript on a product used by whole companies.',
            ],
        );
        foreach ([['Lars', 'Bos', 'lars.bos@mail.nl'], ['Mila', 'Vos', 'mila.vos@mail.nl']] as [$first, $last, $email]) {
            Applicant::firstOrCreate(
                ['company_id' => $company->id, 'email' => $email, 'requisition_id' => $req->id],
                ['first_name' => $first, 'last_name' => $last, 'source' => 'website'],
            );
        }
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
        $pipeline = Pipeline::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Sales pipeline'],
            ['is_default' => true, 'order' => 0],
        );
        Pipeline::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Partnerships'],
            ['is_default' => false, 'order' => 1],
        )->stages()->firstOrCreate(
            ['company_id' => $company->id, 'name' => 'First contact'],
            ['order' => 1, 'probability_default' => 15],
        );

        $stages = collect([['Lead', 1, 10], ['Qualified', 2, 30], ['Proposal', 3, 60], ['Negotiation', 4, 80]])
            ->map(fn (array $row) => PipelineStage::firstOrCreate(
                ['company_id' => $company->id, 'name' => $row[0]],
                ['order' => $row[1], 'probability_default' => $row[2], 'pipeline_id' => $pipeline->id],
            ));
        PipelineStage::query()->whereNull('pipeline_id')
            ->where('company_id', $company->id)
            ->where('name', '!=', 'First contact')
            ->update(['pipeline_id' => $pipeline->id]);

        $org = Account::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Acme Client BV'],
            ['industry' => 'Manufacturing', 'employee_count' => 140, 'website' => 'https://acme-client.nl', 'owner_id' => $ownerUser->id],
        );
        $org2 = Account::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Vermeer Logistics'],
            ['industry' => 'Logistics', 'employee_count' => 80, 'owner_id' => $ownerUser->id],
        );

        $contact = Contact::firstOrCreate(
            ['company_id' => $company->id, 'email' => 'jan@acme-client.nl'],
            ['first_name' => 'Jan', 'last_name' => 'Smit', 'lifecycle_stage' => 'opportunity', 'owner_id' => $ownerUser->id, 'account_id' => $org->id],
        );
        foreach ([
            ['Emma', 'de Boer', 'emma@vermeer-logistics.nl', 'lead', $org2->id],
            ['Pieter', 'Jansen', 'pieter@jansen-media.nl', 'lead', null],
            ['Fleur', 'van Dijk', 'fleur@acme-client.nl', 'customer', $org->id],
            ['Daan', 'Mulder', 'daan@mulder-bouw.nl', 'mql', null],
        ] as [$first, $last, $email, $stage, $accountId]) {
            Contact::firstOrCreate(
                ['company_id' => $company->id, 'email' => $email],
                ['first_name' => $first, 'last_name' => $last, 'lifecycle_stage' => $stage, 'owner_id' => $ownerUser->id, 'account_id' => $accountId],
            );
        }

        $dealRows = [
            ['Acme Platform Licence', 2, 1_200_000, $org->id, 'open', null],
            ['Vermeer fleet rollout', 1, 850_000, $org2->id, 'open', null],
            ['Acme support extension', 3, 240_000, $org->id, 'open', null],
            ['Mulder pilot', 0, 90_000, null, 'open', null],
            ['Jansen Media licence', 0, 360_000, null, 'won', now()->subDays(20)],
            ['De Vries webshop', 0, 150_000, null, 'won', now()->subMonths(2)],
        ];
        foreach ($dealRows as [$name, $stageIdx, $cents, $accountId, $status, $closed]) {
            Deal::firstOrCreate(
                ['company_id' => $company->id, 'name' => $name],
                [
                    'stage_id' => $stages[$stageIdx]->id,
                    'contact_id' => $contact->id,
                    'account_id' => $accountId,
                    'owner_id' => $ownerUser->id,
                    'value_cents' => $cents,
                    'probability' => $stages[$stageIdx]->probability_default,
                    'status' => $status,
                    'actual_close_date' => $closed,
                    'expected_close_date' => now()->addMonth(),
                    'stage_entered_at' => now(),
                ],
            );
        }

        // Top-of-funnel leads (crm.leads) — some fresh, some worked, some qualified.
        $leadRows = [
            ['Pieter Hendriks', 'Hendriks Transport', 'pieter@hendriks-transport.nl', 'website', 'new', 120_000],
            ['Sofie Maes', 'Maes Retail Group', 'sofie@maes-retail.be', 'referral', 'working', 450_000],
            ['Thomas Berg', 'Berg Consultancy', 'thomas@bergconsult.nl', 'event', 'qualified', 280_000],
            ['Anna Visser', 'Visser & Co', 'anna@visserco.nl', 'website', 'new', 75_000],
            ['Mark de Wit', 'De Wit Logistics', 'mark@dewit-log.nl', 'manual', 'working', 600_000],
        ];
        foreach ($leadRows as [$name, $companyName, $email, $source, $status, $cents]) {
            Lead::firstOrCreate(
                ['company_id' => $company->id, 'email' => $email],
                [
                    'name' => $name,
                    'company_name' => $companyName,
                    'source' => $source,
                    'status' => $status,
                    'owner_id' => $ownerUser->id,
                    'estimated_value_cents' => $cents,
                ],
            );
        }
    }
}
