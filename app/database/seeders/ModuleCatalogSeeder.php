<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ModuleCatalogEntry;
use Illuminate\Database\Seeder;

/**
 * Seeds the module catalog every canAccess() gates on. Idempotent — upserts
 * by module_key. Prices are euro cents per user per month; 0 = always-free
 * core module (auto-activated on provisioning, never deactivatable).
 * Prices for the paid domain modules are *(assumed)* until the pricing
 * sheet lands — overridable via ADR.
 */
class ModuleCatalogSeeder extends Seeder
{
    /** @var array<string, array{domain: string, name: string, price: int}> */
    public const CATALOG = [
        // Always-free core platform modules
        'core.audit' => ['domain' => 'core', 'name' => 'Audit log', 'price' => 0],
        'core.settings' => ['domain' => 'core', 'name' => 'Company settings', 'price' => 0],
        'core.rbac' => ['domain' => 'core', 'name' => 'Roles & permissions', 'price' => 0],
        'core.invitations' => ['domain' => 'core', 'name' => 'Invitations', 'price' => 0],
        'core.notifications' => ['domain' => 'core', 'name' => 'Notifications', 'price' => 0],
        'core.files' => ['domain' => 'core', 'name' => 'File storage', 'price' => 0],
        'core.marketplace' => ['domain' => 'core', 'name' => 'Module marketplace', 'price' => 0],
        'core.billing' => ['domain' => 'core', 'name' => 'Billing', 'price' => 0],

        // First paid business modules (phase 2 slice) — prices *(assumed)*
        'hr.profiles' => ['domain' => 'hr', 'name' => 'Employee profiles', 'price' => 400],
        'hr.leave' => ['domain' => 'hr', 'name' => 'Leave & absence', 'price' => 300],
        'finance.ledger' => ['domain' => 'finance', 'name' => 'General ledger', 'price' => 500],
        'finance.invoicing' => ['domain' => 'finance', 'name' => 'Invoicing', 'price' => 500],
        'crm.contacts' => ['domain' => 'crm', 'name' => 'Contacts & accounts', 'price' => 400],
        'crm.deals' => ['domain' => 'crm', 'name' => 'Deals', 'price' => 400],
        'crm.pipeline' => ['domain' => 'crm', 'name' => 'Pipeline board', 'price' => 100],
        'crm.activities' => ['domain' => 'crm', 'name' => 'Activities & tasks', 'price' => 200],
        'finance.bank' => ['domain' => 'finance', 'name' => 'Bank accounts', 'price' => 300],
        'finance.expenses' => ['domain' => 'finance', 'name' => 'Expenses', 'price' => 300],
        'hr.onboarding' => ['domain' => 'hr', 'name' => 'Onboarding', 'price' => 300],
    ];

    public function run(): void
    {
        foreach (self::CATALOG as $key => $entry) {
            ModuleCatalogEntry::query()->updateOrCreate(
                ['module_key' => $key],
                [
                    'domain' => $entry['domain'],
                    'name' => $entry['name'],
                    'per_user_monthly_price' => $entry['price'],
                    'is_active' => true,
                ],
            );
        }
    }
}
