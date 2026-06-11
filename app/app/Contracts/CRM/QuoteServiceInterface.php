<?php

declare(strict_types=1);

namespace App\Contracts\CRM;

use App\Data\CRM\CreateQuoteData;
use App\Models\CRM\Quote;

interface QuoteServiceInterface
{
    public function create(CreateQuoteData $data): Quote;

    /** Assigns quote number + single-use accept token, transitions to sent. */
    public function send(string $quoteId): Quote;

    /** Public path — resolves by token (no session), marks accepted, consumes the token. */
    public function acceptByToken(string $token): Quote;
}
