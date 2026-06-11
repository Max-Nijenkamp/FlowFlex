<?php

declare(strict_types=1);

namespace App\Contracts\Finance;

use App\Data\Finance\CreateInvoiceData;
use App\Data\Finance\RecordPaymentData;
use App\Models\Finance\Invoice;

interface InvoiceServiceInterface
{
    public function create(CreateInvoiceData $data): Invoice;

    /** Assigns the gap-free invoice number, transitions to sent. */
    public function send(string $invoiceId): Invoice;

    /** Posts AR/cash journal entry; fires InvoicePaid at zero balance. */
    public function recordPayment(RecordPaymentData $data): Invoice;

    /** Throws CannotVoidPaidInvoiceException. */
    public function void(string $invoiceId, string $reason): Invoice;
}
