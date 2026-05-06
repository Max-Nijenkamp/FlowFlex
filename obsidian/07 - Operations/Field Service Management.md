---
tags: [flowflex, domain/operations, field-service, dispatch, phase/5]
domain: Operations & Field Service
panel: operations
color: "#D97706"
status: planned
last_updated: 2026-05-06
---

# Field Service Management

Dispatch technicians, track them live, and get digital job sign-off on mobile.

**Who uses it:** Dispatch, field technicians, operations managers
**Filament Panel:** `operations`
**Depends on:** [[Asset Management]]
**Phase:** 5
**Build complexity:** Very High — 4 resources, 3 pages, 9 tables

## Events Fired

- `FieldJobDispatched`
- `FieldJobCompleted` → consumed by [[Invoicing]] (creates invoice), [[Inventory Management]] (deducts parts used), [[Customer Support & Helpdesk]] (closes related support ticket)

## Events Consumed

- `TicketCreated` (from [[Customer Support & Helpdesk]]) → auto-creates field job if type = on-site visit

## Database Tables (9)

1. `field_jobs` — job records with status workflow
2. `field_job_technicians` — technician assignments per job
3. `field_job_parts` — parts used on-site per job
4. `field_job_signatures` — customer digital signatures
5. `technician_locations` — GPS location logs
6. `job_routes` — optimised route records
7. `job_photos` — photos taken on-site during job
8. `job_checklists` — completion checklist templates
9. `job_checklist_responses` — completed checklist records per job

## Features

- **Job dispatch** — assign jobs to technicians
- **Live map** — GPS locations of all field team members
- **Route optimisation** — shortest route to next job
- **Mobile job app** — offline-capable, works without signal
- **Customer arrival notification** — SMS when technician is on the way
- **Digital job completion sign-off** — customer signature on mobile
- **Parts used on-site** — technician logs parts, deducts from [[Inventory Management]]

## Related

- [[Operations Overview]]
- [[Asset Management]]
- [[Equipment Maintenance]]
- [[Inventory Management]]
- [[Invoicing]]
- [[Customer Support & Helpdesk]]
