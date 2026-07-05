<?php

declare(strict_types=1);

namespace App\Contracts\Finance;

use App\Data\Finance\CreateInvoiceData;
use App\Data\Finance\RecordPaymentData;
use App\Models\Finance\Invoice;

interface InvoiceServiceInterface
{
    public function create(CreateInvoiceData $data): Invoice;

    /** Assigns the gap-free number, transitions to sent, queues the mail. */
    public function send(string $invoiceId): Invoice;

    /** Partial payments supported; completing payment fires InvoicePaid + posts the journal. */
    public function recordPayment(RecordPaymentData $data): Invoice;

    /** Draft/sent/overdue only; a sent void posts a reversal. */
    public function void(string $invoiceId, string $reason): Invoice;
}
