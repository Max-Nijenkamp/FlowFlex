---
domain: ecommerce
module: abandoned-cart
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Abandoned Cart — Architecture

## Cart Lifecycle

```
active ──inactive > window──> abandoned ──recovery link used──> recovered
   │                              │
   └────── order placed ──────────┴──> converted (sequence stops)
```

Plain enum on `ec_carts.status` (`active/abandoned/recovered/converted`).

## Services & Actions

| Class | Responsibility |
|---|---|
| `CartRecoveryService::detect()` | active carts past the inactivity window → `abandoned` |
| `CartRecoveryService::advance()` | send due steps per schedule, once each (unique step rows); stop on `converted`/`recovered` |
| `RestoreCartController` | public signed-token link → session cart; sets `recovered` on the subsequent order |
| conversion check | order matching email/token → `converted`, sequence stops |

## Events

None fired/consumed — the v1 `CartAbandoned` event was dropped; recovery is same-domain. See [[../../../../architecture/event-bus]] and [[../orders/decisions|Orders ADR]].

## Filament Artifacts

| Artifact | Nav group | ui-strategy | Notes |
|---|---|---|---|
| `AbandonedCartResource` | Marketing | simple-resource (read-only) | status, recovery funnel |
| `CartRecoveryWidget` | Marketing | widget | recovery rate, revenue recovered |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('ecommerce.abandoned-cart.view')
        && BillingService::hasModule('ecommerce.abandoned-cart');
}
```

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Cart status flips (`detect` / recovery / conversion) | Pessimistic | `lockForUpdate` on the cart row -- a raced recovery + conversion must resolve to one terminal status per the lifecycle enum |
| Step sends (`advance`) | n-a | Unique `(cart_id, step)` rows make sends idempotent; single scheduled writer |
| `PruneCartsCommand` | n-a | Single-writer daily delete with 90d guard |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `ProcessAbandonedCartsCommand` | notifications | every 15 min | unique step rows + status guards |
| `PruneCartsCommand` | default | daily | 90d guard |

## Search & Realtime

None.
