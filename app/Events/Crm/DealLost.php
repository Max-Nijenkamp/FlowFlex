<?php

namespace App\Events\Crm;

use App\Models\Crm\Deal;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DealLost
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Deal $deal) {}
}
