---
type: module
domain: HR & People
panel: hr
module-key: hr.self-service
status: planned
color: "#4ADE80"
---

# Employee Self-Service

Employee-facing portal for viewing personal info, submitting leave requests, downloading payslips, and completing onboarding tasks. Lives inside the `/hr` panel, scoped to the logged-in employee's own data only.

---

## Core Features

- Personal info view/edit: name, address, emergency contacts, bank details (read-only sensitive fields)
- Leave: submit leave requests, view balance, request history
- Payslips: download historical payslips PDF
- Onboarding tasks: complete assigned tasks from the onboarding plan
- Documents: view and download personal documents (contract, payslips, certifications)
- Profile photo upload
- Permission: `hr.self-service.view` — every employee gets this by default

---

## Data Model

No additional tables — reads from existing HR tables scoped to `Auth::user()->employee`.

---

## Filament

**Nav group:** (top-level, always visible to all authenticated users in `/hr` panel)

- `SelfServiceDashboardPage` (custom page) — overview tiles (leave balance, next payslip, pending tasks)
- All self-service views enforce `WHERE employee.user_id = auth()->id()` — employees cannot see other employees' data

---

## Related

- [[domains/hr/leave-management]]
- [[domains/hr/payroll]]
- [[domains/hr/onboarding]]
