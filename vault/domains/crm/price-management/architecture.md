---
domain: crm
module: price-management
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

**Nav group:** Settings

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `ProductResource` | #1 CRUD resource | tweaks: inline-relation-repeater (volume-discount tiers relation manager) | catalogue with an active toggle; unique SKU per company; `cost_cents` role-restricted |
| `PriceBookResource` | #1 CRUD resource | tweaks: inline-relation-repeater (price-book entries with promo `valid_from`/`valid_until` windows) | price books; single-default invariant per company |
| `VolumeDiscountResource` | #1 CRUD resource | standard resource (also usable as a relation manager on `ProductResource`) | volume tiers `(min_quantity, discount_percent)` |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('crm.pricing.view-any') && BillingService::hasModule('crm.pricing')`
per [[../../../architecture/filament-patterns]] #1. There are no custom pages or public/portal surfaces. Cost and
margin data is company-confidential — `manage-price-books` is role-restricted so `cost_cents` is not exposed to
line reps (see [[./security]]).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Product CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Price book / entry CRUD (money — `price_cents`) | Optimistic | `updated_at` stale-check — price config is ordinary CRUD, not a money mutation ([[../../../architecture/patterns/optimistic-locking]]) |
| Volume discount tier CRUD | Optimistic | `updated_at` stale-check ([[../../../architecture/patterns/optimistic-locking]]) |
| Price-book assignment (`AssignPriceBookAction`) | Optimistic | `updated_at` stale-check on the assignment row ([[../../../architecture/patterns/optimistic-locking]]) |
| CPQ resolution (`PricingService::resolve`) | n-a | read-only compute over own price tables — returns a DTO, writes no rows |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

None.

## Search & Realtime

None.
