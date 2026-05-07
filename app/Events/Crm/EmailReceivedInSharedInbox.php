<?php

namespace App\Events\Crm;

use App\Models\Crm\InboxEmail;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailReceivedInSharedInbox
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly InboxEmail $inboxEmail) {}
}
