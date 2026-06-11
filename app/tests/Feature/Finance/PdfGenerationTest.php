<?php

declare(strict_types=1);

use App\Actions\Finance\GenerateInvoicePdfAction;
use App\Actions\HR\GeneratePayslipPdfAction;
use App\Contracts\Finance\InvoiceServiceInterface;
use App\Contracts\HR\PayrollServiceInterface;
use App\Data\Finance\CreateInvoiceData;
use App\Data\HR\CreatePayrollRunData;
use App\Models\Company;
use App\Models\Finance\Customer;
use App\Models\HR\Employee;
use App\Models\HR\PayrollEmployee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\LaravelPdf\Facades\Pdf;

uses(RefreshDatabase::class);

beforeEach(function () {
    Pdf::fake();
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
    $this->actingAs(User::factory()->forCompany($this->company)->create(), 'web');
});

it('generates a tenant-scoped invoice PDF and stores the path', function () {
    $customer = Customer::factory()->forCompany($this->company)->create();
    $invoice = app(InvoiceServiceInterface::class)->create(new CreateInvoiceData(
        customer_id: $customer->id, issue_date: now()->toDateString(),
        lines: [['description' => 'Work', 'quantity' => 1, 'unit_price_cents' => 50000]],
    ));
    $invoice = app(InvoiceServiceInterface::class)->send($invoice->id);

    $path = GenerateInvoicePdfAction::run($invoice->id);

    expect($path)->toStartWith("companies/{$this->company->id}/")
        ->and($invoice->fresh()->pdf_path)->toBe($path);
    Pdf::assertViewIs('pdf.invoice');
});

it('generates a tenant-scoped payslip PDF', function () {
    $employee = Employee::factory()->forCompany($this->company)->create();
    PayrollEmployee::factory()->create([
        'company_id' => $this->company->id, 'employee_id' => $employee->id, 'salary_raw' => '300000',
    ]);
    $payroll = app(PayrollServiceInterface::class);
    $run = $payroll->createRun(new CreatePayrollRunData(
        period_start: '2026-06-01', period_end: '2026-06-30', employee_ids: [$employee->id],
    ));
    $run = $payroll->processRun($run->id);
    $payslip = $run->payslips()->first();

    $path = GeneratePayslipPdfAction::run($payslip->id);

    expect($path)->toStartWith("companies/{$this->company->id}/")
        ->and($payslip->fresh()->pdf_path)->toBe($path);
    Pdf::assertViewIs('pdf.payslip');
});
