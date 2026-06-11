---
type: module
domain: CRM & Sales
domain-key: crm
panel: crm
module-key: crm.segments
status: complete
priority: v1
depends-on: [crm.contacts, core.billing, core.rbac]
soft-depends: [crm.sequences, marketing.campaigns, comms.broadcast]
fires-events: []
consumes-events: []
patterns: [custom-fields]
tables: [crm_segments, crm_segment_members]
permission-prefix: crm.segments
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Customer Segments

Dynamic contact segments based on attributes and behaviour. Used to target campaigns, assign playbooks, and filter lists.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/contacts\|crm.contacts]] | segments select contacts/accounts |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/crm/sales-sequences\|crm.sequences]], [[domains/marketing/campaigns\|marketing.campaigns]], [[domains/communications/broadcast\|communications.broadcast]] | consumers of segment audiences |

---

## Core Features

- Segment builder: filter conditions on contact/account attributes
- Conditions: industry, size, lifecycle stage, deal value, last activity, tag, location, custom fields
- Dynamic membership: resolved as a query at read time — never materialised for dynamic segments *(assumed: `member_count` cached snapshot only)*
- Static lists: manually curated contact lists (alternative to dynamic)
- Segment size preview before saving
- Use in: Marketing campaigns, CRM sequences, broadcasts (consumers call `SegmentService::contacts()`)
- Combine conditions with AND/OR logic (one nesting level *(assumed)*)
- Segment overlap analysis

---

## Data Model

### crm_segments

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | unique per company |
| type | string | dynamic / static |
| conditions | jsonb | {logic: and/or, rules: [{field, operator, value}]} — dynamic only |
| member_count | int | cached snapshot, refreshed nightly |
| deleted_at | timestamp nullable | |

### crm_segment_members (static lists only)

| Column | Type | Notes |
|---|---|---|
| id, segment_id FK, company_id, contact_id FK | ulid | unique `(segment_id, contact_id)` |

---

## DTOs

### CreateSegmentData — name (unique per company), type (in:dynamic,static), conditions (required_if dynamic; fields/operators validated against allowed registry — including custom-field keys)
Allowed operators: equals, not-equals, contains, gt, lt, in, has-tag, days-since-activity-gt *(assumed set)*.

## Services & Actions

- `SegmentService::contacts(string $segmentId): Builder` — dynamic: conditions → query builder (CompanyScope inherent); static: membership join. **The single audience API for all consumers.**
- `SegmentService::preview(array $conditions): int`
- `SegmentService::overlap(string $a, string $b): int`
- `AddToStaticSegmentAction::run(string $segmentId, array $contactIds)` / remove

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `RefreshSegmentCountsCommand` | default | nightly 02:30 | recompute snapshot counts — deterministic |

---

## Filament

**Nav group:** Contacts

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SegmentResource` | #1 CRUD resource | condition builder (repeater), live preview count, members relation for static |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('crm.segments.view-any') && BillingService::hasModule('crm.segments')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`crm.segments.view-any` · `crm.segments.create` · `crm.segments.update` · `crm.segments.delete`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Dynamic membership reflects data changes immediately (query-time)
- [ ] Condition operators each produce correct SQL (fixture per operator, incl. custom-field JSONB)
- [ ] AND/OR logic correct
- [ ] Static add/remove; duplicate member rejected
- [ ] Invalid field/operator rejected at save
- [ ] Preview equals actual count

---

## Build Manifest

```
database/migrations/xxxx_create_crm_segments_table.php
database/migrations/xxxx_create_crm_segment_members_table.php
app/Models/CRM/{Segment,SegmentMember}.php
app/Data/CRM/CreateSegmentData.php
app/Services/CRM/SegmentService.php
app/Support/CRM/SegmentConditionCompiler.php
app/Actions/CRM/AddToStaticSegmentAction.php
app/Console/Commands/CRM/RefreshSegmentCountsCommand.php
app/Filament/CRM/Resources/SegmentResource.php
database/factories/CRM/SegmentFactory.php
tests/Feature/CRM/{SegmentConditionTest,SegmentMembershipTest}.php
```

---

## Related

- [[domains/crm/contacts]]
- [[domains/marketing/campaigns]]
- [[architecture/patterns/custom-fields]]
