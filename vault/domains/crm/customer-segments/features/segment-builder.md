---
domain: crm
module: customer-segments
type: feature
feature: segment-builder
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature — Segment Builder

The segment builder lets users define a dynamic audience by composing filter conditions over contact and account attributes.

## Conditions

Fields available include industry, size, lifecycle stage, deal value, last activity, tag, location, and custom fields. Each rule is `{field, operator, value}`.

Operators *(assumed set)*: `equals`, `not-equals`, `contains`, `gt`, `lt`, `in`, `has-tag`, `days-since-activity-gt`.

## Logic

Rules combine with AND/OR at a single nesting level *(assumed)*. The rule tree is stored as `conditions` JSONB: `{logic: and/or, rules:[...]}`.

## Compilation

`SegmentConditionCompiler` translates the rule tree into a scoped query builder. Every field and operator is validated against an allowed registry (including custom-field keys) before SQL is generated — invalid entries are rejected at save.

## Live preview

Before saving, the builder shows a live count via `SegmentService::preview(conditions)`, so users see the audience size their conditions produce.

## UI
- **Kind**: custom-page — a query/filter builder UI for composing the rule tree (a CRUD form can't express the AND/OR condition tree + live preview).
- **Page**: Segment Builder page → reached from `SegmentResource` create/edit (custom builder page or a schema section on the resource).
- **Layout**: rule rows (`field` · `operator` · `value`), AND/OR group toggle (single nesting level), live audience-size preview panel.
- **Key interactions**: add/remove rule, pick field + operator (validated against the allowed registry incl. custom-field keys), toggle AND/OR, watch preview count update, save.
- **States**: empty (no rules → whole-audience or zero preview) · loading (preview count query) · error (invalid field/operator rejected at save) · selected (active rule row focused)
- **Gating**: `crm.segments.create` / `crm.segments.update`

## Data
- Owns / writes: `crm_segments` (stores `conditions` JSONB rule tree, `type`, cached `member_count`)
- Reads: [[../../contacts/_module|crm.contacts]] (+ contact custom fields, tags) at preview/compile time — read-only, never written
- Cross-domain writes: via events only ([[../../../../security/data-ownership]])

## Relations
- Consumes: contact attribute/custom-field schema (owned by contacts)
- Feeds: saved segment → audience consumed by [[../../sales-sequences/_module|crm.sequences]] and marketing/broadcast via `SegmentService::contacts()`
- Shared entity: contacts; tags ([[../../../../architecture/patterns/custom-fields]])

## Related

- [[../architecture]]
- [[dynamic-vs-static]]
- [[../../../../architecture/patterns/custom-fields]]
