---
tags: [flowflex, domain/operations, assets, phase/4]
domain: Operations & Field Service
panel: operations
color: "#D97706"
status: planned
last_updated: 2026-05-06
---

# Asset Management

Track every physical asset — where it is, who has it, its condition and lifecycle stage.

**Who uses it:** Operations managers, IT team, HR
**Filament Panel:** `operations`
**Depends on:** Core
**Phase:** 4

## Events Consumed

- `OffboardingCompleted` (from [[Offboarding]]) → creates asset recall task for all assets assigned to departing employee
- `EmployeeHired` (from [[Recruitment & ATS]]) → triggers equipment request task in onboarding

## Features

- **Asset register** — every physical asset with make, model, serial number, purchase date, cost
- **Assignment** — assign asset to employee or location
- **QR code asset labels** — print labels, scan to view/update asset record
- **Lifecycle stages** — in use / in storage / under maintenance / disposed
- **Check-out / check-in workflow** — formal handover with signature
- **IT asset tracking** — linked to [[IT Asset Management]] for software/licence tracking
- **Disposal recording** — write-off or sale, feeds to [[Fixed Asset & Depreciation]]

## Related

- [[Operations Overview]]
- [[IT Asset Management]]
- [[Fixed Asset & Depreciation]]
- [[Equipment Maintenance]]
- [[Offboarding]]
