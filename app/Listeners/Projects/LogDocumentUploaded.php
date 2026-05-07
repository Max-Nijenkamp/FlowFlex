<?php

namespace App\Listeners\Projects;

use App\Events\Projects\DocumentUploaded;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogDocumentUploaded implements ShouldQueue
{
    public function handle(DocumentUploaded $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
