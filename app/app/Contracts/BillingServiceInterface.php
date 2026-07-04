<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\BillingInvoice;
use App\Models\CompanyModuleSubscription;
use App\Models\User;
use Brick\Money\Money;
use Carbon\CarbonImmutable;

/**
 * Billing engine contract (interface-service pattern): module gating,
 * monthly invoicing, Stripe webhook routing, suspension, admin metrics.
 */
interface BillingServiceInterface
{
    public function hasModule(string $moduleKey): bool;

    /** @return list<string> */
    public function activeModules(string $companyId): array;

    public function activateModule(string $moduleKey, User $actor): CompanyModuleSubscription;

    public function deactivateModule(string $moduleKey): void;

    public function generateMonthlyInvoice(string $companyId, CarbonImmutable $period): ?BillingInvoice;

    /** @param array<string, mixed> $event */
    public function handleStripeWebhook(array $event): void;

    public function suspend(string $companyId, string $reason): void;

    public function mrr(): Money;

    public function churnRate(CarbonImmutable $period): float;
}
