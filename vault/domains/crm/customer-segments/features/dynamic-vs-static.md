---
domain: crm
module: customer-segments
type: feature
feature: dynamic-vs-static
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Dynamic vs Static Segments

A segment is either dynamic or static, chosen at creation via `type`.

## Dynamic

- Stores a `conditions` rule tree.
- Membership is resolved as a scoped query at read time — never materialised.
- Always reflects current data; a contact that starts matching the conditions is automatically included.
- `member_count` is a nightly cached snapshot *(assumed)*, not used for resolution.

## Static

- Manually curated list, stored in `crm_segment_members`.
- Membership only changes when a user adds/removes contacts (`AddToStaticSegmentAction::run` / `remove`).
- Duplicate members are rejected via the unique `(segment_id, contact_id)` index.
- Suited to audiences that should not drift with data changes.

## Consumption

Both types are resolved through the same audience API, `SegmentService::contacts(segmentId)`. Dynamic compiles conditions to SQL; static performs a membership join. Consumers never need to know which type a segment is.

## UI
- **Kind**: simple-resource — a segment record with a `type` toggle on `SegmentResource`. (Dynamic segments re-evaluate on a schedule via a background `RefreshSegmentCountsCommand`; static are point-in-time snapshots.)
- **Page**: `SegmentResource` list/create/edit under the CRM panel.
- **Layout**: segment list (name, type badge, `member_count`); create/edit form with `type` toggle → dynamic reveals the builder, static reveals a member-management list.
- **Key interactions**: choose dynamic vs static; for dynamic, edit conditions (builder); for static, add/remove members (`AddToStaticSegmentAction`); view member count.
- **States**: empty (no segments) · loading (count refresh) · error (duplicate static member rejected via unique `(segment_id, contact_id)`) · selected (segment row opened)
- **Gating**: `crm.segments.view` / `crm.segments.update`

## Data
- Owns / writes: `crm_segments` (`type`, `conditions`, cached `member_count`), `crm_segment_members` (static membership only; dynamic never materialised)
- Reads: [[../../contacts/_module|crm.contacts]] at resolution time for dynamic query / static join — read-only
- Cross-domain writes: via events only ([[../../../../security/data-ownership]])

## Relations
- Consumes: contact-change events *(assumed)* → re-evaluate dynamic segment counts; scheduled tick → `RefreshSegmentCountsCommand`
- Feeds: resolved audience via `SegmentService::contacts()` → [[../../sales-sequences/_module|crm.sequences]], marketing, broadcast
- Shared entity: contacts

## Test Checklist

### Unit
- [ ] Dynamic resolves via a scoped query at read time (never materialised); static reads the membership join
- [ ] Duplicate static member rejected by the unique `(segment_id, contact_id)` index

### Feature (Pest)
- [ ] `SegmentService::contacts()` returns the same audience shape for dynamic and static segments
- [ ] Tenant isolation: a compiled dynamic query never reaches another company's contacts even with a cross-tenant id in `conditions`

### Livewire
- [ ] `type` toggle reveals the builder (dynamic) vs the member list (static); edit denied without `crm.segments.update`

## Related

- [[../architecture]]
- [[../data-model]]
- [[segment-builder]]
