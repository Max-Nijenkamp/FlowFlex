<?php

declare(strict_types=1);

namespace App\Contracts\CRM;

use App\Models\CRM\Deal;
use Brick\Money\Money;

interface DealServiceInterface
{
    public function create(string $name, string $stageId, int $valueCents, ?string $contactId = null, ?string $accountId = null, ?string $expectedCloseDate = null): Deal;

    /** Stage move within open — throws ClosedDealImmutableException when closed. */
    public function moveToStage(string $dealId, string $stageId): Deal;

    /** Fires DealWon. */
    public function win(string $dealId): Deal;

    /** Requires reason; fires DealLost. */
    public function lose(string $dealId, string $reason): Deal;

    public function weightedPipelineValue(?string $ownerId = null): Money;
}
