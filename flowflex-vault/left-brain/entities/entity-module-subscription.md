---
type: entity
domain: Core Platform
table: company_module_subscriptions
primary_key: ulid
soft_deletes: false
last_updated: 2026-05-08
---

# Entity: Module Subscription

Controls which modules a company has enabled. Each active module is billed at its per-user monthly rate × company's active user count. No fixed plans — fully à la carte.

**Table:** `company_module_subscriptions`  
**Multi-Tenant:** Yes — `company_id`.

---

## Schema

```erDiagram
    company_module_subscriptions {
        ulid id PK
        ulid company_id FK
        string module_key
        string status
        timestamp activated_at
        timestamp deactivated_at
        json settings
    }

    companies ||--o{ company_module_subscriptions : "controls"
```

---

## Key Columns

| Column | Type | Notes |
|---|---|---|
| `module_key` | string | e.g. `hr.payroll`, `finance.invoicing`, `crm.pipeline` |
| `status` | enum | `active`, `inactive`, `trial`, `suspended` |
| `activated_at` | timestamp | When company first enabled this module |
| `settings` | JSON | Module-specific config (e.g. tax rates, default currency) |

---

## Module Key Format

`{domain}.{module}` — e.g.:
- `hr.payroll`
- `hr.leave`
- `finance.invoicing`
- `crm.pipeline`
- `marketing.email`

---

## How Module Access Works

```php
// Gate check in AuthServiceProvider
Gate::define('access.hr-panel', function (User $user) {
    return $user->company->hasModule('hr.core');
});

// Company model helper
public function hasModule(string $key): bool
{
    return $this->moduleSubscriptions()
        ->where('module_key', $key)
        ->where('status', 'active')
        ->exists();
}
```

Filament panel only renders if company has the panel's core module active.  
Individual features within a panel check specific module keys.

---

## Billing Model

Billing = sum over all active modules of `module_catalog.per_user_monthly_price × company.active_user_count`

Monthly Stripe invoice is generated per company from this calculation. See [[entity-module-catalog]] for price definitions.

---

## Business Rules

1. On company creation → activate modules selected by admin at creation time
2. Any module can be activated or deactivated at any time by admin
3. Deactivating module → `deactivated_at` set, data retained (not deleted)
4. Re-activating restores all data
5. Billing updates immediately on activation/deactivation (prorate in Stripe)

---

## Related

- [[MOC_Entities]]
- [[entity-company]]
- [[entity-module-catalog]] — pricing definitions per module
- [[MOC_CorePlatform]]
- [[auth-rbac]]
