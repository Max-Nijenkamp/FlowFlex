---
type: entity
domain: Core Platform
table: company_module_subscriptions
primary_key: ulid
soft_deletes: false
last_updated: 2026-05-08
---

# Entity: Module Subscription

Controls which modules a company has enabled. The gate between plan limits and actual feature access.

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

## Business Rules

1. On company creation → bootstrap starter plan modules (auto-activate)
2. Upgrading plan → activate additional modules
3. Deactivating module → `deactivated_at` set, data retained (not deleted)
4. Re-activating restores all data

---

## Related

- [[MOC_Entities]]
- [[entity-company]]
- [[MOC_CorePlatform]]
- [[auth-rbac]]
