---
domain: crm
module: customer-segments
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Customer Segments — Architecture

## State Machine

None. Segments have a `type` (dynamic/static) but no lifecycle transitions.

## Services & Actions

| Class | Method | Purpose |
|---|---|---|
| `SegmentService` | `contacts(segmentId): Builder` | The single audience API for all consumers. Dynamic → compiles `conditions` to a query builder (with `CompanyScope`); static → membership join. |
| `SegmentService` | `preview(conditions): int` | Count matching contacts for a candidate condition set before save. |
| `SegmentService` | `overlap(a, b): int` | Count contacts shared by two segments. |
| `SegmentConditionCompiler` | `compile(conditions, Builder): Builder` | Translates `{logic, rules[]}` JSON into scoped SQL; validates each field/operator against the allowed registry (incl. custom-field keys). |
| `AddToStaticSegmentAction` | `run(segmentId, contactIds)` | Add contacts to a static list (duplicate members rejected). |
| `AddToStaticSegmentAction` | `remove(segmentId, contactIds)` | Remove contacts from a static list. |

`SegmentService::contacts()` is the one audience API — Marketing campaigns, CRM sequences, and broadcasts all call it rather than reimplementing filtering.

## Events

Fires: none. Consumes: none.

## Filament Artifacts

**Nav group:** Contacts

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `SegmentResource` | #1 CRUD resource | tweaks: inline-relation-repeater (condition rule rows), inline-relation-repeater (static-list members) | live audience preview via `SegmentService::preview`; static-list members relation |

The rule-tree builder + live count on the create/edit form is modelled here as a repeater on `SegmentResource` (see [[./features/segment-builder]], which flags a Report Builder / Query UI custom-page shape as an alternative — resolved by ADR, tracked in [[./unknowns]]).

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('crm.segments.view-any') && BillingService::hasModule('crm.segments')`
per [[../../../architecture/filament-patterns]] #1. Custom pages MUST state this explicitly — Filament does not auto-gate them.

```php
public static function canAccess(): bool
{
    return auth()->user()?->can('crm.segments.view-any')
        && hasModule('crm.segments');
}
```

See [[../../../architecture/filament-patterns]] and [[../../../architecture/ui-strategy]].

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Segment CRUD (name, `type`, `conditions`) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Static membership add/remove (`AddToStaticSegmentAction`) | Optimistic | `updated_at` stale-check on the segment; duplicate members rejected by the unique `(segment_id, contact_id)` index (append-safe) |
| Dynamic resolution + `RefreshSegmentCountsCommand` | n-a | read-only derived query / scheduled recompute of cached `member_count` — no user-facing concurrent write |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

| Job | Queue | Schedule | Purpose |
|---|---|---|---|
| `RefreshSegmentCountsCommand` | default | Nightly 02:30 | Recompute `member_count` snapshot for all segments — deterministic recompute. |

See [[../../../infrastructure/queue-horizon]].

## Search & Realtime

None. Segment resolution is a scoped Eloquent query; no Meilisearch index or websocket channel.
