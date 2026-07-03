---
domain: marketing
module: landing-pages
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Landing Pages — Architecture

Parent: [[_module]]

## Services & Actions

| Class | Signature | Responsibility |
|---|---|---|
| `LandingPageService::publish` | `publish(pageId)` | Validate all blocks against `BlockRegistry`; `draft → published`, stamp `published_at`. |
| `LandingPageService::unpublish` | `unpublish(pageId)` | `published → draft`. |
| `RecordVisitAction` | public | Increment `visit_count`; (soft) capture UTM via marketing.utm. |
| `BlockRegistry` | support | Typed block schema registry; each block config schema-validated on save. |

Conversion attribution: a form submission carrying the page ref counts as a page conversion (read-only aggregation — the submission is owned by Forms).

## Public render

`GET /p/{company-slug}/{page-slug}` — Vue + Inertia block renderer (ui-strategy row #16). Published pages only; draft → 404. Per-IP throttle.

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `LandingPageResource` | Landing Pages | #1 CRUD resource | block **repeater** builder, preview, publish; visit/conversion columns |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('marketing.landing-pages.view-any')
        && BillingService::hasModule('marketing.landing-pages');
}
```

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Page CRUD / block builder save | Optimistic | Version-checked save per [[../../../architecture/patterns/optimistic-locking]] |
| Publish / unpublish (`LandingPageService`) | Pessimistic | State transition draft<->published — `lockForUpdate` in transaction per patterns/states |
| `RecordVisitAction` visit_count increment | n-a | Atomic SQL increment on the public path; no read-modify-write race |
| Conversion aggregation | n-a | Read-only over Forms-owned submissions |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Related

- [[_module]] · [[data-model]] · [[../forms/_module]] · [[../../../frontend/_index]]
