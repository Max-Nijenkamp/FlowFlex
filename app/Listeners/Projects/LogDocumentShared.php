<?php

namespace App\Listeners\Projects;

use App\Events\Projects\DocumentShared;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogDocumentShared implements ShouldQueue
{
    public function handle(DocumentShared $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
