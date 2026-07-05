<?php

declare(strict_types=1);

namespace App\Contracts\Crm;

use App\Data\Crm\CloseDealData;
use App\Data\Crm\CreateDealData;
use App\Models\Crm\Deal;
use Brick\Money\Money;

interface DealServiceInterface
{
    public function create(CreateDealData $data): Deal;

    public function moveToStage(string $dealId, string $stageId): Deal;

    public function close(CloseDealData $data): Deal;

    public function duplicate(string $dealId): Deal;

    public function weightedPipelineValue(): Money;
}
