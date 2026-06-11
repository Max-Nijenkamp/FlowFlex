<?php

declare(strict_types=1);

namespace App\Contracts\Core;

use App\Data\Core\ActivateModuleData;
use App\Data\Core\BillingInvoiceData;
use Carbon\CarbonImmutable;

interface BillingServiceInterface
{
    /** The single gating check called by every canAccess(). Cached 5 min. */
    public function hasModule(string $moduleKey): bool;

    /** @return list<string> active module keys for the current company (cached) */
    public function activeModuleKeys(): array;

    public function activateModule(ActivateModuleData $data): void;

    public function deactivateModule(string $moduleKey): void;

    /** Seed the free core modules as active for a freshly created company. */
    public function seedFreeCoreModules(string $companyId): void;

    /** Idempotent per (company, period) — unique constraint backs re-runs. */
    public function generateMonthlyInvoice(string $companyId, CarbonImmutable $period): BillingInvoiceData;

    /** Signature already verified upstream by the webhook middleware. @param array<string, mixed> $event */
    public function handleStripeWebhook(array $event): void;

    public function suspend(string $companyId, string $reason): void;
}
