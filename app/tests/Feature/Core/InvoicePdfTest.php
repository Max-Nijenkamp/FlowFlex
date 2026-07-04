<?php

declare(strict_types=1);

use App\Actions\RenderInvoicePdfAction;
use App\Mail\InvoiceMail;
use App\Models\BillingInvoice;
use App\Models\BillingInvoiceLine;
use App\Models\Company;
use App\States\Core\BillingInvoice\Open;
use App\Support\Services\CompanyContext;

function pdfInvoice(): BillingInvoice
{
    $company = setCompany(Company::factory()->create(['name' => 'Acme BV', 'currency' => 'EUR']));

    $invoice = BillingInvoice::query()->create([
        'company_id' => $company->id,
        'period_start' => '2026-06-01',
        'period_end' => '2026-06-30',
        'total_cents' => 2100,
        'currency' => 'EUR',
        'status' => Open::class,
    ]);

    BillingInvoiceLine::query()->create([
        'invoice_id' => $invoice->id,
        'company_id' => $company->id,
        'module_key' => 'hr.leave',
        'module_name' => 'Leave & absence',
        'user_count' => 7,
        'unit_price_cents' => 300,
        'line_total_cents' => 2100,
    ]);

    return $invoice;
}

test('an invoice renders to a real PDF with a stable human-readable number', function () {
    $invoice = pdfInvoice();

    $pdf = RenderInvoicePdfAction::run($invoice);

    expect(str_starts_with($pdf, '%PDF'))->toBeTrue()
        ->and(strlen($pdf))->toBeGreaterThan(1000)
        ->and(RenderInvoicePdfAction::number($invoice))->toStartWith('FF-202606-')
        ->and(RenderInvoicePdfAction::number($invoice))->toHaveLength(16);
});

test('the PDF renders without tenant context — admin panel and queued mail paths', function () {
    $invoice = pdfInvoice();
    app(CompanyContext::class)->forget();

    $pdf = RenderInvoicePdfAction::run($invoice);

    expect(str_starts_with($pdf, '%PDF'))->toBeTrue();
});

test('the invoice mail carries the PDF as an attachment', function () {
    $invoice = pdfInvoice();

    $mail = new InvoiceMail($invoice->id, '€ 21,00', 'June 2026');

    expect($mail->attachments())->toHaveCount(1);
});
