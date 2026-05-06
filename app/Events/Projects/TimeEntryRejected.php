<?php

namespace App\Events\Projects;

use App\Models\Projects\TimeEntry;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TimeEntryRejected
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly TimeEntry $entry) {}
}
