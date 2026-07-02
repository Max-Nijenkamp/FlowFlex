---
domain: analytics
module: dashboards
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Custom Dashboards — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../security/data-ownership]], [[../../../architecture/filament-patterns]].

---

## Permissions

| Permission | Description |
|---|---|
| `analytics.dashboards.view-any` | View dashboards (own + shared) |
| `analytics.dashboards.create` | Create a new dashboard |
| `analytics.dashboards.update-own` | Edit / add widgets to a dashboard you own |
| `analytics.dashboards.delete-own` | Delete a dashboard you own *(assumed)* |
| `analytics.dashboards.manage-shared` | Toggle sharing / manage team-shared dashboards |

Seeded in `PermissionSeeder`. **Verb-per-command:** the share toggle is a distinct command gated by `manage-shared` (not folded into `update-own`); ownership scoping (`update-own`/`delete-own` vs the shared audience) per [[../../../architecture/patterns/policy]].

---

## Rate Limiting

No rate-limited actions in this module: widgets are read-only cached resolutions and dashboards generate no files, comms, or money/inventory mutations. Report/export generation lives in [[../../scheduled-exports/_module|analytics.exports]] and [[../../report-builder/_module|analytics.reports]], which cite the `exports` limiter. If a future "export this dashboard" action is added it MUST cite the `exports` limiter (5/hr per company) per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]. *(assumed — no such action in v1)*

---

## Access Contract

Every Filament artifact gates on:

```php
canAccess() = Auth::user()->can('analytics.dashboards.view-any')
           && BillingService::hasModule('analytics.dashboards')
```

Per [[../../../architecture/filament-patterns]] #1 — custom pages (`DashboardBuilderPage`) must state `canAccess()` explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

---

## The domain-defining control: data-source validation

The single most important security control in Analytics is that the widget `data_source` JSON is
**validated against the `MetricRegistry` on write**:

- An **unregistered** metric key is rejected — a widget can never carry a free-form query.
- A metric whose **module is inactive** is rejected/hidden — deactivating a module removes its data.
- Filters are validated against the metric definition's `allowed_filters` — no arbitrary filter injection.

Because the only data path is a registered closure that runs in the owning domain under
`CompanyContext`, Analytics **cannot** read or write another domain's tables directly
([[../../../security/data-ownership]]). This is the boundary the whole domain rests on.

---

## Tenant Isolation

- `bi_dashboards` and `bi_widgets` scoped by `company_id` via `BelongsToCompany` + `CompanyScope`.
- Every metric closure runs under the owning domain's `CompanyContext` — no side-door that skips the company scope.
- **Sharing is intra-company only**: a shared dashboard is visible to same-company users with `view-any`; never cross-company.
- Private dashboards are invisible to everyone but the owner; shared dashboards are read-only to non-owners.

See [[../../../security/tenancy-isolation]] and [[../../../architecture/multi-tenancy]].
