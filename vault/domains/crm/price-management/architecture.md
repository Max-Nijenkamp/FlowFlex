---
domain: crm
module: price-management
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Price Management — Architecture

## State Machine

None. Products and price books use a simple `is_active` / `is_default` boolean model, not a lifecycle state machine.

## Services & Actions

| Class | Signature | Notes |
|---|---|---|
| `PricingService` | `resolve(ResolvePriceData): PriceResolutionData` | CPQ resolution — see order below. Monetary math via `brick/money`. |
| `AssignPriceBookAction` | `run(bookId, accountOrSegmentId): void` | Binds a price book to an account or segment. |

**Resolution order** (first match wins for the base price):

1. Account's assigned price book →
2. Segment price book →
3. Default price book →
4. Product standard price.

Then apply the highest-qualifying volume tier, then run the margin check against `cost_cents` + a threshold. All arithmetic uses integer minor units via `brick/money` — never raw float math. See [[../../../architecture/filament-patterns]] and [[../../../glossary]].

## Events

None fired, none consumed.

## Filament Artifacts

Nav group: **Settings**.

| Artifact | ui-strategy row | Purpose |
|---|---|---|
| `ProductResource` | #1 CRUD | Catalogue with an active toggle. |
| `PriceBookResource` | #1 CRUD | Price books; entries relation manager with promo windows. |
| `VolumeDiscountResource` | #1 CRUD | Volume tiers (standalone or as a relation manager on Product). |

Follows [[../../../architecture/ui-strategy]] and [[../../../architecture/filament-patterns]].

**Access contract:** `canAccess()` = `can('crm.pricing.view-any') && hasModule('crm.pricing')`.

## Jobs & Scheduling

None.

## Search & Realtime

None.
