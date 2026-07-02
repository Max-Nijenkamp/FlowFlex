---
domain: workplace
module: visitor-management
feature: visitor-log
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Visitor Log

The audit record of who visited, when, and who they saw — for security and compliance.

## Behaviour

- Chronological log of visitors with host, expected/in/out times, badge, company, purpose.
- Filterable by date range, host, company; exportable for compliance.
- Reads are permission-gated; PII shows decrypted only to authorised staff.

## UI

- **Kind**: simple-resource (log view / filtered table)
- **Page**: `VisitorResource` list with log filters at `/workplace/visitors`.
- **Layout**: dense table (visitor, company, host, in, out, badge, purpose); date-range + host + company filters; export action.
- **Key interactions**: filter → export; click row → visit detail infolist.
- **States**: empty (no visits in range) · loading (skeleton) · error (toast) · selected (row → detail).
- **Gating**: `workplace.visitors.view-any`; export requires `workplace.visitors.manage`.

## Data

- Owns / writes: nothing (read-only over `wp_visitors`).
- Reads: `wp_visitors` (own module); `hr.profiles` for host names (read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: check-in/out timestamps from [[check-in]].
- Feeds: visitor-volume trends read by [[../../workplace-analytics/_module|Workplace Analytics]].
- Shared entity: `hr_employees` (host) — owned by [[../../../hr/employee-profiles/_module|hr.profiles]], read-only.

## Related

- [[../_module|Visitor Management]] · [[check-in]] · [[gdpr-purge]] · [[../security]]
