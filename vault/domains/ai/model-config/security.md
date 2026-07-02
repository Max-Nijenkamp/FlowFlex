---
domain: ai
module: model-config
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# AI Model Configuration — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../security/data-ownership]], [[../../../architecture/patterns/encryption]].

---

## Permissions

| Permission | Description |
|---|---|
| `ai.config.manage` | Configure provider, models, keys, budget, toggles |
| `ai.config.view-usage` | View the usage/cost dashboard |

---

## Access Contract

```php
canAccess() = Auth::user()->can('ai.config.view-any')
           && BillingService::hasModule('ai.config')
```

Per [[../../../architecture/filament-patterns]] #1 — both custom pages state `canAccess()` explicitly.

---

## Secret Handling

- `ai_config.api_key` uses the `encrypted` cast on a `text` column ([[../../../architecture/patterns/encryption]]).
- The key is **verified with a test call before save** and is **never re-displayed** — the config form field is write-only (blank on load; empty submit keeps the stored key).
- Provider keys are per-company (BYO-key v1 *(assumed)*); a compromise in one company cannot read another's key (CompanyScope + row-level isolation).

---

## Tenant Isolation & Budget as a Control

- All rows scoped by `company_id` via `BelongsToCompany` + `CompanyScope`; `ai_config` is unique per company.
- The **budget hard-stop is a cost-abuse control**, not just a setting: `LlmGateway` refuses calls once the monthly token budget is exceeded, capping runaway/hostile usage per tenant.
- `LlmGateway` runs under `CompanyContext`, so the usage log and key resolution always bind to the acting company — no side-door around the scope.

See [[../../../security/tenancy-isolation]] and [[../../../architecture/multi-tenancy]].
