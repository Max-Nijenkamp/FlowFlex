<?php

declare(strict_types=1);

namespace App\Actions\Finance;

use App\Models\Company;
use App\Models\Finance\Customer;
use App\Models\Finance\Invoice;
use App\Models\Finance\InvoiceLine;
use Barryvdh\DomPDF\Facade\Pdf;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Renders a customer invoice to PDF bytes (finance.invoicing). Dompdf
 * per ADR 2026-07-04 dompdf-for-invoice-pdfs; rendered on demand — the
 * PDF is derivable, storing it adds a stale-copy risk. Unscoped company
 * fetch by explicit key so queued mail renders without tenant context.
 */
class RenderCustomerInvoicePdfAction
{
    use AsAction;

    public function handle(Invoice $invoice): string
    {
        $invoice->loadMissing(['lines', 'customer']);

        /** @var Customer|null $customer */
        $customer = $invoice->customer()->first();

        $company = Company::query()
            ->withoutGlobalScopes()
            ->whereKey($invoice->company_id)
            ->firstOrFail();

        $format = fn (int $cents): string => Money::ofMinor($cents, $invoice->currency)->formatToLocale('nl_NL');

        /** @var Collection<int, InvoiceLine> $lines */
        $lines = $invoice->lines()->get();

        return Pdf::loadView('pdf.customer-invoice', [
            'number' => $invoice->invoice_number ?? 'DRAFT',
            'companyName' => $company->name,
            'customerName' => $customer->name ?? '',
            'customerVat' => $customer->vat_number ?? null,
            'issueDate' => $invoice->issue_date->format('d M Y'),
            'dueDate' => $invoice->due_date->format('d M Y'),
            'statusLabel' => str((string) $invoice->status)->replace('_', ' ')->headline()->toString(),
            'isPaid' => (string) $invoice->status === 'paid',
            'lines' => $lines->map(fn (InvoiceLine $line): array => [
                'description' => $line->description,
                'quantity' => rtrim(rtrim((string) $line->quantity, '0'), '.'),
                'unit' => $format($line->unit_price_cents),
                'tax_rate' => rtrim(rtrim((string) $line->tax_rate_percent, '0'), '.').'%',
                'total' => $format($line->line_total_cents),
            ])->all(),
            'subtotal' => $format($invoice->subtotal_cents),
            'taxTotal' => $format($invoice->tax_total_cents),
            'total' => $format($invoice->total_cents),
            'notes' => $invoice->notes,
        ])->setPaper('a4')->output();
    }
}
