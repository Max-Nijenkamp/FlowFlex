<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\BillingInvoice;
use App\Models\BillingInvoiceLine;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Brick\Money\Money;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Renders an invoice to PDF bytes (core.billing-engine/monthly-invoicing).
 * Dompdf, not spatie/laravel-pdf — the container has no headless Chrome
 * (ADR 2026-07-04 dompdf-for-invoice-pdfs). Company is fetched unscoped so
 * the admin panel and queued mail can render without tenant context.
 */
class RenderInvoicePdfAction
{
    use AsAction;

    public function handle(BillingInvoice $invoice): string
    {
        $invoice->loadMissing('lines');

        // Deliberate unscoped fetch by explicit key — the invoice row itself
        // is the tenant anchor here (tenant-context-pitfalls.md pattern).
        $company = Company::query()
            ->withoutGlobalScopes()
            ->whereKey($invoice->company_id)
            ->firstOrFail();

        $format = fn (int $cents): string => Money::ofMinor($cents, $invoice->currency)->formatToLocale('nl_NL');

        return Pdf::loadView('pdf.invoice', [
            'number' => self::number($invoice),
            'companyName' => $company->name,
            'periodLabel' => $invoice->period_start->format('F Y'),
            'periodRange' => $invoice->period_start->format('d M Y').' — '.$invoice->period_end->format('d M Y'),
            'statusLabel' => str((string) $invoice->status)->replace('_', ' ')->headline()->toString(),
            'isPaid' => (string) $invoice->status === 'paid',
            'paidAt' => $invoice->paid_at?->format('d M Y'),
            'issuedAt' => $invoice->created_at?->format('d M Y') ?? $invoice->period_end->format('d M Y'),
            'lines' => $invoice->lines->map(fn (BillingInvoiceLine $line): array => [
                'name' => $line->module_name,
                'count' => $line->user_count,
                'unit' => $format($line->unit_price_cents),
                'total' => $format($line->line_total_cents),
            ])->all(),
            'total' => $format($invoice->total_cents),
        ])->setPaper('a4')->output();
    }

    /** Stable human-readable number: FF-{period}-{id tail}, e.g. FF-202607-9X2K4M. */
    public static function number(BillingInvoice $invoice): string
    {
        return 'FF-'.$invoice->period_start->format('Ym').'-'.strtoupper(substr($invoice->id, -6));
    }
}
