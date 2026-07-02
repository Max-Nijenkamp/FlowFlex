---
domain: workplace
module: maintenance
feature: report-request
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Report a Request

Any user logs a facility issue with location, category, priority, and photos.

## Behaviour

- Reporter submits `ReportMaintenanceData` (location, category, description, priority, photos).
- The request is created in state `reported`.
- Photos stored via Media Library (image MIME, size cap, tenant path).
- A reporter sees their own requests; facility staff see all.

## UI

- **Kind**: simple-resource
- **Page**: `MaintenanceRequestResource` create/list at `/workplace/maintenance`.
- **Layout**: table with queue tabs (open / assigned / overdue); form with category select, priority, description, photo upload.
- **Key interactions**: "Log an issue" -> form -> photo upload -> submit; row -> detail infolist.
- **States**: empty (no requests -> "log your first issue" CTA) - loading (skeleton) - error (upload/validation toast) - selected (row -> detail).
- **Gating**: log via `workplace.maintenance.report` (all users); view others via `workplace.maintenance.view-any`.

## Data

- Owns / writes: `wp_maintenance_requests` only.
- Reads: `users` for reporter (read-only).
- Cross-domain writes: none - photos via `core.files` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: request volume read by [[../../workplace-analytics/_module|Workplace Analytics]].
- Shared entity: none.

## Test Checklist

### Unit
- [ ] `ReportMaintenanceData` validates category/priority; photo MIME whitelist (jpg/png/webp) + size cap

### Feature (Pest)
- [ ] Submit creates a `reported` request; photos land under `companies/{id}/maintenance/`
- [ ] Reporter sees only own requests; `view-any` sees all (ownership scope)

### Livewire
- [ ] Create form validates + uploads; oversized/wrong-MIME photo rejected with human copy
- [ ] Log denied without `workplace.maintenance.report`

## Related

- [[../_module|Facility Maintenance]] - [[assignment-workflow]] - [[../api]]
