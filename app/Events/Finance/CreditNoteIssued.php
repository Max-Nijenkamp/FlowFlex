<?php

namespace App\Events\Finance;

use App\Models\Finance\CreditNote;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreditNoteIssued
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly CreditNote $creditNote) {}
}
