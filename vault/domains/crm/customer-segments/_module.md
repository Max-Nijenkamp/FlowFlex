---
domain: crm
module: customer-segments
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Customer Segments

Dynamic contact segments based on attributes and behaviour. Used to target campaigns, assign playbooks, and filter lists.

> This module is planned for rebuild. Prior "shipped/complete" references reflect the stripped codebase; see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] for context.

## Module-key

`crm.segments`

**Priority:** v1  
**Panel:** crm  
**Permission prefix:** `crm.segments`  
**Tables:** `crm_segments`, `crm_segment_members`

## Dependencies

| Kind | Module | Why |
|---|---|---|
| Hard | [[../../crm/contacts/_module\|Contacts]] | Segments select contacts/accounts as their audience |
| Hard | [[../../core/billing/_module\|Billing]] | Module gating (`hasModule`) |
| Hard | [[../../core/rbac/_module\|RBAC]] | Permissions |
| Soft | [[../../crm/sales-sequences/_module\|Sales Sequences]] | Consumer of segment audiences (segment-entry triggers) |
| Soft | [[../../marketing/campaigns/_module\|Marketing Campaigns]] | Consumer of segment audiences (campaign targeting) |
| Soft | [[../../communications/broadcast/_module\|Broadcast]] | Consumer of segment audiences (broadcast recipients) |

## Core Features

- Segment builder — filter conditions on contact/account attributes.
- Conditions across industry, size, lifecycle stage, deal value, last activity, tag, location, and custom fields.
- Dynamic membership resolved as a query at read time — never materialised for dynamic segments *(assumed: member_count cached snapshot only)*.
- Static lists — manually curated, an alternative to dynamic segments.
- Segment size preview before saving.
- Use in Marketing campaigns / CRM sequences / broadcasts — consumers call `SegmentService::contacts()`.
- Combine conditions AND/OR — one nesting level *(assumed)*.
- Segment overlap analysis.

## See features/

- [[features/segment-builder|Segment builder]]
- [[features/dynamic-vs-static|Dynamic vs static]]

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

## Test Checklist

- [ ] Tenant isolation: company A cannot see or resolve company B's segments/members
- [ ] Module gating: artifacts hidden when `crm.segments` inactive
- [ ] Dynamic membership reflects data changes immediately (query-time).
- [ ] Condition operators each produce correct SQL (fixture per operator, incl. custom-field JSONB).
- [ ] AND/OR logic correct.
- [ ] Static add/remove works, and duplicate member is rejected.
- [ ] Invalid field/operator rejected at save.
- [ ] Preview equals actual count.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | contact read API (+ custom fields, tags) | [[../contacts/_module\|crm.contacts]] | conditions compile over contact attributes |
| Feeds | segment audience API `SegmentService::contacts()` | [[../sales-sequences/_module\|crm.sequences]], marketing campaigns, broadcast | consumers enrol / target a segment |
| Consumes | contact-change events *(assumed)* | [[../contacts/_module\|crm.contacts]] | re-evaluate dynamic segment membership/counts |
| Consumes | scheduled tick | `RefreshSegmentCountsCommand` | recompute cached `member_count` for dynamic segments |

**Data ownership:** `crm.segments` writes only `crm_segments`, `crm_segment_members`; all cross-domain effects go through events / owning-service APIs ([[../../../security/data-ownership]]).

## Related

- [[../../crm/contacts/_module|Contacts]]
- [[../../marketing/campaigns/_module|Marketing Campaigns]]
- [[../../../architecture/patterns/custom-fields]]
- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
