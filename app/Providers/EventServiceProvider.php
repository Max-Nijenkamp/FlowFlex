<?php

namespace App\Providers;

use App\Events\Crm\DealLost;
use App\Events\Crm\DealWon;
use App\Events\Crm\EmailReceivedInSharedInbox;
use App\Events\Crm\TicketResolved;
use App\Events\Finance\CreditNoteIssued;
use App\Events\Finance\ExpenseApproved;
use App\Events\Finance\ExpenseRejected;
use App\Events\Finance\ExpenseSubmitted;
use App\Events\Finance\InvoiceCreated;
use App\Events\Finance\InvoiceOverdue;
use App\Events\Finance\InvoicePaid;
use App\Events\Finance\InvoiceSent;
use App\Events\Hr\EmployeeDepartmentChanged;
use App\Events\Hr\EmployeeProfileCreated;
use App\Events\Hr\EmployeeProfileUpdated;
use App\Events\Hr\EmployeeRoleChanged;
use App\Events\Hr\LeaveApproved;
use App\Events\Hr\LeaveBalanceLow;
use App\Events\Hr\LeaveRejected;
use App\Events\Hr\LeaveRequested;
use App\Events\Hr\OnboardingCompleted;
use App\Events\Hr\OnboardingStarted;
use App\Events\Hr\OnboardingTaskCompleted;
use App\Events\Hr\PayRunApproved;
use App\Events\Hr\PayRunCreated;
use App\Events\Hr\PayRunProcessed;
use App\Events\Hr\PayslipGenerated;
use App\Events\Projects\DocumentShared;
use App\Events\Projects\DocumentUploaded;
use App\Events\Projects\DocumentVersioned;
use App\Events\Projects\TaskAssigned;
use App\Events\Projects\TaskCompleted;
use App\Events\Projects\TaskCreated;
use App\Events\Projects\TaskOverdue;
use App\Events\Projects\TimeEntryApproved;
use App\Events\Projects\TimeEntryCreated;
use App\Events\Projects\TimeEntryRejected;
use App\Listeners\Crm\LogDealLost;
use App\Listeners\Crm\LogDealWon;
use App\Listeners\Crm\NotifyTicketResolved;
use App\Listeners\Finance\LogCreditNoteIssued;
use App\Listeners\Finance\LogInvoiceCreated;
use App\Listeners\Finance\LogInvoicePaid;
use App\Listeners\Finance\LogInvoiceSent;
use App\Listeners\Finance\NotifyExpenseApproved;
use App\Listeners\Finance\NotifyExpenseRejected;
use App\Listeners\Finance\NotifyExpenseSubmitted;
use App\Listeners\Finance\NotifyInvoiceOverdue;
use App\Listeners\Hr\DispatchPayslipGenerationJobs;
use App\Listeners\Hr\LogEmployeeDepartmentChanged;
use App\Listeners\Hr\LogEmployeeProfileCreated;
use App\Listeners\Hr\LogEmployeeProfileUpdated;
use App\Listeners\Hr\LogEmployeeRoleChanged;
use App\Listeners\Hr\LogOnboardingCompleted;
use App\Listeners\Hr\LogPayRunApproved;
use App\Listeners\Hr\LogPayRunCreated;
use App\Listeners\Hr\NotifyEmployeeLeaveApproved;
use App\Listeners\Hr\NotifyEmployeeLeaveRejected;
use App\Listeners\Hr\NotifyEmployeeOnboardingStarted;
use App\Listeners\Hr\NotifyEmployeePayslipGenerated;
use App\Listeners\Hr\NotifyHrOnboardingTaskCompleted;
use App\Listeners\Hr\NotifyManagerLeaveBalanceLow;
use App\Listeners\Hr\NotifyManagerOfLeaveRequest;
use App\Listeners\Projects\LogDocumentShared;
use App\Listeners\Projects\LogDocumentUploaded;
use App\Listeners\Projects\LogDocumentVersioned;
use App\Listeners\Projects\LogTaskCompleted;
use App\Listeners\Projects\LogTaskCreated;
use App\Listeners\Projects\LogTaskOverdue;
use App\Listeners\Projects\LogTimeEntryApproved;
use App\Listeners\Projects\LogTimeEntryCreated;
use App\Listeners\Projects\LogTimeEntryRejected;
use App\Listeners\Projects\NotifyAssigneeTaskAssigned;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // HR — Leave
        LeaveRequested::class => [
            NotifyManagerOfLeaveRequest::class,
        ],
        LeaveApproved::class => [
            NotifyEmployeeLeaveApproved::class,
        ],
        LeaveRejected::class => [
            NotifyEmployeeLeaveRejected::class,
        ],
        LeaveBalanceLow::class => [
            NotifyManagerLeaveBalanceLow::class,
        ],

        // HR — Employee
        EmployeeDepartmentChanged::class => [
            LogEmployeeDepartmentChanged::class,
        ],
        EmployeeProfileCreated::class => [
            LogEmployeeProfileCreated::class,
        ],
        EmployeeProfileUpdated::class => [
            LogEmployeeProfileUpdated::class,
        ],
        EmployeeRoleChanged::class => [
            LogEmployeeRoleChanged::class,
        ],

        // HR — Onboarding
        OnboardingStarted::class => [
            NotifyEmployeeOnboardingStarted::class,
        ],
        OnboardingTaskCompleted::class => [
            NotifyHrOnboardingTaskCompleted::class,
        ],
        OnboardingCompleted::class => [
            LogOnboardingCompleted::class,
        ],

        // HR — Payroll
        PayRunCreated::class => [
            LogPayRunCreated::class,
        ],
        PayRunApproved::class => [
            LogPayRunApproved::class,
        ],
        PayRunProcessed::class => [
            DispatchPayslipGenerationJobs::class,
        ],
        PayslipGenerated::class => [
            NotifyEmployeePayslipGenerated::class,
        ],

        // Projects — Tasks
        TaskAssigned::class => [
            NotifyAssigneeTaskAssigned::class,
        ],
        TaskCreated::class => [
            LogTaskCreated::class,
        ],
        TaskCompleted::class => [
            LogTaskCompleted::class,
        ],
        TaskOverdue::class => [
            LogTaskOverdue::class,
        ],

        // Projects — Documents
        DocumentShared::class => [
            LogDocumentShared::class,
        ],
        DocumentUploaded::class => [
            LogDocumentUploaded::class,
        ],
        DocumentVersioned::class => [
            LogDocumentVersioned::class,
        ],

        // Projects — Time Entries
        TimeEntryCreated::class => [
            LogTimeEntryCreated::class,
        ],
        TimeEntryApproved::class => [
            LogTimeEntryApproved::class,
        ],
        TimeEntryRejected::class => [
            LogTimeEntryRejected::class,
        ],

        // Finance — Invoices
        InvoiceCreated::class => [
            LogInvoiceCreated::class,
        ],
        InvoiceSent::class => [
            LogInvoiceSent::class,
        ],
        InvoicePaid::class => [
            LogInvoicePaid::class,
        ],
        InvoiceOverdue::class => [
            NotifyInvoiceOverdue::class,
        ],

        // Finance — Credit Notes
        CreditNoteIssued::class => [
            LogCreditNoteIssued::class,
        ],

        // Finance — Expenses
        ExpenseSubmitted::class => [
            NotifyExpenseSubmitted::class,
        ],
        ExpenseApproved::class => [
            NotifyExpenseApproved::class,
        ],
        ExpenseRejected::class => [
            NotifyExpenseRejected::class,
        ],

        // CRM
        TicketResolved::class => [
            NotifyTicketResolved::class,
        ],
        DealWon::class => [
            LogDealWon::class,
        ],
        DealLost::class => [
            LogDealLost::class,
        ],

        // CRM — Shared Inbox (stub; listeners wired in Phase 8)
        EmailReceivedInSharedInbox::class => [],
    ];
}
