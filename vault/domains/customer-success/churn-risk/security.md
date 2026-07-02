---
domain: customer-success
module: churn-risk
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Churn Risk — Security

## Permissions

| Permission | Description |
|---|---|
| `cs.churn.view-any` | View the at-risk account queue and risk detail |
| `cs.churn.resolve` | Manually resolve a risk and run a recovery playbook |

Seeded in `PermissionSeeder`.

---

## Access Contract

```php
canAccess() = Auth::user()->can('cs.churn.view-any')
           && BillingService::hasModule('cs.churn')
```

Per [[../../../architecture/filament-patterns]] #1. The resolve + run-recovery-playbook actions additionally require `cs.churn.resolve`. Running a recovery playbook is further gated on `cs.playbooks` being active (soft-dep bridge) and, at execution, on `cs.playbooks` own permissions.

---

## Tenant Isolation

- `cs_churn_risks` carries `company_id` with a global `CompanyScope` — see [[../../../architecture/multi-tenancy]].
- The partial-unique open-risk constraint is scoped by `company_id`.
- The evaluation job runs under `WithCompanyContext`, one company at a time.
- Risk-factor reads (health, NPS, finance, engagement) go through each owning domain's tenant-scoped read API — no cross-company leakage.

---

## Rate Limiting

Not applicable. No public/portal endpoints; the only mutating surfaces are the gated resolve + run-recovery actions inside the panel.

---

## Encrypted Fields

None. Risk levels and factor breakdowns are derived operational signals, not sensitive personal data.
