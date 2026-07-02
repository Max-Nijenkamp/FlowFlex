---
domain: hr
module: employee-self-service
feature: my-payslips
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# My Payslips

**Purpose.** Let the employee download their own historical payslips as PDF.

**Behavior.** Next-payslip tile on `SelfServiceDashboardPage`; download streams the auth employee's own payslip only. Tile and downloads render only when `hr.payroll` is active; hidden otherwise (soft-dep degraded behavior).

**Source module.** [[../../payroll/_module]] (soft dependency)

**Permissions.** `hr.self-service.view`.

## UI

- **Kind**: custom-page (soft-dep hr.payroll — page hidden when hr.payroll inactive)
- **Page**: "My Payslips" (`/app/my-payslips`)
- **Layout**: table of own historical payslips (period, net, download PDF).
- **Key interactions**: browse own payslips; download a payslip PDF.
- **States**: empty = "No payslips yet"; loading = skeleton; error = "Could not load"; selected = download PDF stream.
- **Gating**: visible with `hr.self-service.access` AND hr.payroll active; download own payslips only (self-scoped).

  > [!warning] UNVERIFIED
  > Page/tile is hidden entirely when the hr.payroll module is inactive (soft-dep degraded behavior).

## Data

- Owns / writes: none — this module owns no tables.
- Reads: own payslips (owned by hr.payroll) scoped to `Auth::user()->employee`.
- Cross-domain writes: none.

## Relations

- Consumes: none.
- Feeds: none.
- Shared entity: reads hr.payroll payslips.

[[../_module]]
