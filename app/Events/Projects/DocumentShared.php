<?php

namespace App\Events\Projects;

use App\Models\Projects\Document;
use App\Models\Projects\DocumentShare;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentShared
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Document $document,
        public readonly DocumentShare $share,
    ) {}
}
