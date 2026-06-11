<?php

declare(strict_types=1);

namespace App\Actions\Finance;

use App\Models\Finance\Invoice;
use App\Settings\CompanyIdentitySettings;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\LaravelPdf\Facades\Pdf;

class GenerateInvoicePdfAction
{
    use AsAction;

    /** Renders + stores the PDF tenant-scoped, returns the storage path. */
    public function handle(string $invoiceId): string
    {
        $invoice = Invoice::query()->with(['lines', 'customer'])->findOrFail($invoiceId);

        $path = "companies/{$invoice->company_id}/fin_invoices/{$invoice->id}/{$invoice->invoice_number}.pdf";

        Pdf::view('pdf.invoice', [
            'invoice' => $invoice,
            'companyName' => app(CompanyIdentitySettings::class)->name ?: config('app.name'),
        ])->disk(config('filesystems.default'))->save($path);

        $invoice->update(['pdf_path' => $path]);

        return $path;
    }
}
