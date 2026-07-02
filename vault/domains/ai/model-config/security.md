---
domain: ai
module: model-config
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# AI Model Configuration — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../security/data-ownership]], [[../../../architecture/patterns/encryption]].

---

## Permissions

| Permission | Description |
|---|---|
| `ai.config.manage` | Configure provider, models, keys, budget, toggles (gates `AiConfigPage`) |
| `ai.config.view-usage` | View the usage/cost dashboard (gates `AiUsageDashboardPage`) |

Seeded in `PermissionSeeder`. There is no `ai.config.view-any` — each custom page gates on its own verb (config = `manage`, usage = `view-usage`); the two-page split is why this module has no shared list-view permission.

---

## Access Contract

Each custom page states `canAccess()` explicitly (Filament does not auto-gate custom pages):

```php
// AiConfigPage
canAccess() = Auth::user()->can('ai.config.manage')
           && BillingService::hasModule('ai.config')

// AiUsageDashboardPage
canAccess() = Auth::user()->can('ai.config.view-usage')
           && BillingService::hasModule('ai.config')
```

Per [[../../../architecture/filament-patterns]] #1 and [[../../../architecture/patterns/custom-page-checklist]].

---

## Rate Limiting

- The **API-key verification test call** on save reaches an external LLM provider — it MUST carry the named `panel-action` rate limiter ([[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]) so a repeated bad-key save cannot be used to hammer a provider endpoint.
- Runtime LLM calls through `LlmGateway::complete` are bounded primarily by the **monthly token budget hard-stop** (cost control); consuming modules (`ai.copilot`, `ai.document-intelligence`) additionally throttle their own send/upload actions with `panel-action`. The provider key itself is stored encrypted (`ai_config.api_key`, `encrypted` cast) — see **Secret Handling** below.

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
