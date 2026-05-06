<?php

namespace App\Events\Projects;

use App\Models\Projects\Document;
use App\Models\Projects\DocumentVersion;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentVersioned
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Document $document,
        public readonly DocumentVersion $version,
    ) {}
}
