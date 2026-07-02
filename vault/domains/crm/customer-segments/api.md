---
domain: crm
module: customer-segments
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Customer Segments — API

## Input DTOs

### CreateSegmentData

| Field | Type | Validation |
|---|---|---|
| name | string | Unique per company |
| type | string | `in:dynamic,static` |
| conditions | array | `required_if` type=dynamic; fields/operators validated against the allowed registry (incl. custom-field keys) |

Allowed operators *(assumed set)*: `equals`, `not-equals`, `contains`, `gt`, `lt`, `in`, `has-tag`, `days-since-activity-gt`.

## Output DTOs

No dedicated output DTO — resolution returns an Eloquent `Builder` (see below) or scalar counts.

## Audience API

`SegmentService::contacts(segmentId): Builder` is the audience API. All consumers (Marketing campaigns, CRM sequences, broadcasts) call it to obtain the resolved contact set:

- Dynamic segment → `conditions` compiled to a scoped query builder.
- Static segment → membership join over `crm_segment_members`.

`SegmentService::preview(conditions): int` and `SegmentService::overlap(a, b): int` return counts.

## Public / Portal Endpoints

None. Segments are internal CRM tooling with no public or portal-facing API.
