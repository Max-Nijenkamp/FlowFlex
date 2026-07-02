---
domain: crm
module: customer-segments
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
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

| Artifact | Nav group | Kind (ui-strategy) | Notes |
|---|---|---|---|
| `SegmentResource` | Contacts | Standard CRUD resource | Condition builder repeater, live preview count, members relation for static lists |

Access contract:

```php
public static function canAccess(): bool
{
    return auth()->user()?->can('crm.segments.view-any')
        && hasModule('crm.segments');
}
```

See [[../../../architecture/filament-patterns]] and [[../../../architecture/ui-strategy]].

## Jobs & Scheduling

| Job | Queue | Schedule | Purpose |
|---|---|---|---|
| `RefreshSegmentCountsCommand` | default | Nightly 02:30 | Recompute `member_count` snapshot for all segments — deterministic recompute. |

See [[../../../infrastructure/queue-horizon]].

## Search & Realtime

None. Segment resolution is a scoped Eloquent query; no Meilisearch index or websocket channel.
