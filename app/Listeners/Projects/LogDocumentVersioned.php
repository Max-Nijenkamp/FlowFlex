<?php

namespace App\Listeners\Projects;

use App\Events\Projects\DocumentVersioned;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogDocumentVersioned implements ShouldQueue
{
    public function handle(DocumentVersioned $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
