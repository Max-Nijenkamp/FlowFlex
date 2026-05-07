<?php

namespace App\Providers;

use App\Models\ApiKey;
use App\Models\Company;
use App\Models\File;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\ApiKeyPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\FilePolicy;
use App\Policies\ModulePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\TenantPolicy;
use App\Policies\UserPolicy;

// HR models
use App\Models\Hr\Department;
use App\Models\Hr\Employee;
use App\Models\Hr\LeaveRequest;
use App\Models\Hr\LeaveType;
use App\Models\Hr\OnboardingFlow;
use App\Models\Hr\OnboardingTemplate;
use App\Models\Hr\PayElement;
use App\Models\Hr\PayRun;
use App\Models\Hr\Payslip;
use App\Models\Hr\PayrollEntity;
use App\Models\Hr\PublicHoliday;
use App\Models\Hr\SalaryRecord;

// HR models (additional)
use App\Models\Hr\ContractorPayment;
use App\Models\Hr\Deduction;

// HR policies
use App\Policies\Hr\DepartmentPolicy;
use App\Policies\Hr\EmployeePolicy;
use App\Policies\Hr\LeaveRequestPolicy;
use App\Policies\Hr\LeaveTypePolicy;
use App\Policies\Hr\OnboardingFlowPolicy;
use App\Policies\Hr\OnboardingTemplatePolicy;
use App\Policies\Hr\PayElementPolicy;
use App\Policies\Hr\PayRunPolicy;
use App\Policies\Hr\PayslipPolicy;
use App\Policies\Hr\PayrollEntityPolicy;
use App\Policies\Hr\PublicHolidayPolicy;
use App\Policies\Hr\SalaryRecordPolicy;
use App\Policies\Hr\ContractorPaymentPolicy;
use App\Policies\Hr\DeductionPolicy;

// Projects models
use App\Models\Projects\Document;
use App\Models\Projects\DocumentFolder;
use App\Models\Projects\Task;
use App\Models\Projects\TaskAutomation;
use App\Models\Projects\TaskLabel;
use App\Models\Projects\TimeEntry;
use App\Models\Projects\Timesheet;

// Projects policies
use App\Policies\Projects\DocumentFolderPolicy;
use App\Policies\Projects\DocumentPolicy;
use App\Policies\Projects\TaskAutomationPolicy;
use App\Policies\Projects\TaskLabelPolicy;
use App\Policies\Projects\TaskPolicy;
use App\Policies\Projects\TimeEntryPolicy;
use App\Policies\Projects\TimesheetPolicy;

// Finance models
use App\Models\Finance\Invoice;
use App\Models\Finance\Expense;
use App\Models\Finance\ExpenseCategory;
use App\Models\Finance\CreditNote;
use App\Models\Finance\MileageRate;
use App\Models\Finance\ExpenseReport;
use App\Models\Finance\RecurringInvoice;
use App\Models\Finance\InvoiceLine;
use App\Models\Finance\InvoicePayment;
use App\Models\Finance\InvoiceEmailEvent;

// Finance policies
use App\Policies\Finance\InvoicePolicy;
use App\Policies\Finance\ExpensePolicy;
use App\Policies\Finance\ExpenseCategoryPolicy;
use App\Policies\Finance\CreditNotePolicy;
use App\Policies\Finance\MileageRatePolicy;
use App\Policies\Finance\ExpenseReportPolicy;
use App\Policies\Finance\RecurringInvoicePolicy;
use App\Policies\Finance\InvoiceLinePolicy;
use App\Policies\Finance\InvoicePaymentPolicy;
use App\Policies\Finance\InvoiceEmailEventPolicy;

// CRM models
use App\Models\Crm\CrmContact;
use App\Models\Crm\CrmCompany;
use App\Models\Crm\Deal;
use App\Models\Crm\Pipeline;
use App\Models\Crm\DealStage;
use App\Models\Crm\Ticket;
use App\Models\Crm\CannedResponse;
use App\Models\Crm\ChatbotRule;
use App\Models\Crm\TicketSlaRule;

// CRM policies
use App\Policies\Crm\CrmContactPolicy;
use App\Policies\Crm\CrmCompanyPolicy;
use App\Policies\Crm\DealPolicy;
use App\Policies\Crm\PipelinePolicy;
use App\Policies\Crm\DealStagePolicy;
use App\Policies\Crm\TicketPolicy;
use App\Policies\Crm\CannedResponsePolicy;
use App\Policies\Crm\ChatbotRulePolicy;
use App\Policies\Crm\TicketSlaRulePolicy;

use App\Services\FileStorageService;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FileStorageService::class);
    }

    public function boot(): void
    {
        $this->registerPolicies();
        $this->configureDefaults();
        $this->configureLanguageSwitch();
        $this->configureHealthChecks();
    }

    protected function registerPolicies(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Company::class, CompanyPolicy::class);
        Gate::policy(Tenant::class, TenantPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(Module::class, ModulePolicy::class);
        Gate::policy(ApiKey::class, ApiKeyPolicy::class);
        Gate::policy(File::class, FilePolicy::class);

        // HR
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(LeaveRequest::class, LeaveRequestPolicy::class);
        Gate::policy(LeaveType::class, LeaveTypePolicy::class);
        Gate::policy(OnboardingFlow::class, OnboardingFlowPolicy::class);
        Gate::policy(OnboardingTemplate::class, OnboardingTemplatePolicy::class);
        Gate::policy(PayElement::class, PayElementPolicy::class);
        Gate::policy(PayRun::class, PayRunPolicy::class);
        Gate::policy(Payslip::class, PayslipPolicy::class);
        Gate::policy(PayrollEntity::class, PayrollEntityPolicy::class);
        Gate::policy(PublicHoliday::class, PublicHolidayPolicy::class);
        Gate::policy(SalaryRecord::class, SalaryRecordPolicy::class);
        Gate::policy(ContractorPayment::class, ContractorPaymentPolicy::class);
        Gate::policy(Deduction::class, DeductionPolicy::class);

        // Projects
        Gate::policy(Document::class, DocumentPolicy::class);
        Gate::policy(DocumentFolder::class, DocumentFolderPolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(TaskAutomation::class, TaskAutomationPolicy::class);
        Gate::policy(TaskLabel::class, TaskLabelPolicy::class);
        Gate::policy(TimeEntry::class, TimeEntryPolicy::class);
        Gate::policy(Timesheet::class, TimesheetPolicy::class);

        // Finance
        Gate::policy(Invoice::class, InvoicePolicy::class);
        Gate::policy(Expense::class, ExpensePolicy::class);
        Gate::policy(ExpenseCategory::class, ExpenseCategoryPolicy::class);
        Gate::policy(CreditNote::class, CreditNotePolicy::class);
        Gate::policy(MileageRate::class, MileageRatePolicy::class);
        Gate::policy(ExpenseReport::class, ExpenseReportPolicy::class);
        Gate::policy(RecurringInvoice::class, RecurringInvoicePolicy::class);
        Gate::policy(InvoiceLine::class, InvoiceLinePolicy::class);
        Gate::policy(InvoicePayment::class, InvoicePaymentPolicy::class);
        Gate::policy(InvoiceEmailEvent::class, InvoiceEmailEventPolicy::class);

        // CRM
        Gate::policy(CrmContact::class, CrmContactPolicy::class);
        Gate::policy(CrmCompany::class, CrmCompanyPolicy::class);
        Gate::policy(Deal::class, DealPolicy::class);
        Gate::policy(Pipeline::class, PipelinePolicy::class);
        Gate::policy(DealStage::class, DealStagePolicy::class);
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::policy(CannedResponse::class, CannedResponsePolicy::class);
        Gate::policy(ChatbotRule::class, ChatbotRulePolicy::class);
        Gate::policy(TicketSlaRule::class, TicketSlaRulePolicy::class);
    }

    protected function configureLanguageSwitch(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'nl', 'de'])
                ->visible(insidePanels: true, outsidePanels: false)
                ->nativeLabel()
                ->flags([
                    'en' => asset('flags/gb.svg'),
                    'nl' => asset('flags/nl.svg'),
                    'de' => asset('flags/de.svg'),
                ]);
        });
    }

    protected function configureHealthChecks(): void
    {
        $checks = [
            DatabaseCheck::new(),
            CacheCheck::new(),
            UsedDiskSpaceCheck::new()
                ->warnWhenUsedSpaceIsAbovePercentage(70)
                ->failWhenUsedSpaceIsAbovePercentage(90),
        ];

        // Add Redis check only when the extension is available (production)
        if (extension_loaded('redis')) {
            $checks[] = \Spatie\Health\Checks\Checks\RedisCheck::new();
        }

        Health::checks($checks);
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }


}
