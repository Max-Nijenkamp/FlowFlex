---
domain: hr
module: org-chart
feature: export
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Export (PNG/PDF)

## Purpose

Download the rendered org chart as an image or PDF.

## Intended Behavior

- Export the current (possibly department-filtered) tree to PNG/PDF.
- Rendering mechanism *(assumed: client-side render-to-image)* — see [[../unknowns]].

## UI

- **Kind**: background (a page action that generates PNG/PDF/CSV; a heavy render may queue)
- **Page**: Export action on the Org Chart page (`/hr/org-chart`)
- **Layout**: Export button → format menu (PNG / PDF / CSV); generates a downloadable file (spatie/laravel-pdf for PDF; client-side or server render for PNG).
- **Key interactions**: Click Export, choose a format, receive the download.
- **States**: empty = disabled when the tree is empty; loading = "Generating…" with progress; error = "Export failed, retry"; selected = download-ready notification.
- **Gating**: visible with `hr.org.view`; export requires `hr.org.export` *(assumed)*.

> [!warning] UNVERIFIED
> Whether PNG is rendered client-side (render-to-image in the browser) or as a queued server job is unconfirmed. PDF path likely uses spatie/laravel-pdf server-side; PNG/CSV mechanism *(assumed)*.

## Data

- Owns / writes: none (view-only module)
- Reads: `hr_employees` (self-referential `manager_id` for hierarchy) + `hr_departments`, both owned by [[../../employee-profiles/_module|hr.profiles]], read via `EmployeeService` / `OrgChartService`.
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]]).

## Relations

- Consumes: none
- Feeds: none
- Shared entity: reads the rendered tree (from hr.profiles data — `hr_employees`, `hr_departments`).

## Test Checklist

### Unit
- [ ] Export targets the current (possibly department-filtered) tree, not always the full company tree

### Feature (Pest)
- [ ] Export generates a downloadable file for the rendered tree (PDF via spatie/laravel-pdf; PNG/CSV per *(assumed)* mechanism)
- [ ] Export is throttled by the named `exports` rate limiter (see [[../security]])
- [ ] Tenant isolation: an export contains only the acting company's employees

### Livewire
- [ ] Export action gated on `hr.org.export`; disabled when the tree is empty

## Related

- Permissions: `hr.org.view`, `hr.org.export`
- [[../_module]]
- [[../unknowns]]
