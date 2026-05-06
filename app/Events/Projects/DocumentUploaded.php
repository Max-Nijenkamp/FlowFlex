<?php

namespace App\Events\Projects;

use App\Models\Projects\Document;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentUploaded
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Document $document) {}
}
