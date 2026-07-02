---
domain: hr
module: employee-self-service
feature: my-profile
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# My Profile

**Purpose.** Let the employee view and edit their own personal info: phone, personal email, emergency contacts, and profile photo. Sensitive fields (bank details, national_id) are read-only.

**Behavior.** `MyProfilePage` (#7 custom form page) edits an own-profile slice via `UpdateOwnProfileData` → `UpdateOwnProfileAction`. Employees may **not** edit name, email, job, salary, department, manager, national_id (HR-only, rejected). Photo upload supported. Emergency contacts array (max 3 *(assumed)*).

**Source module.** [[../../employee-profiles/_module]]

**Permissions.** `hr.self-service.update-own` (plus `hr.self-service.view`).

## UI

- **Kind**: custom-page
- **Page**: "My Profile" (`/app/my-profile`)
- **Layout**: read-mostly profile card (name, employee number, job title, department, manager, contact) with a narrow editable own-slice (personal_email, phone, emergency contacts, photo); sensitive/HR-only fields (salary, national_id, bank details) shown read-only.
- **Key interactions**: view own record; toggle edit on own-slice fields; update phone/personal_email/emergency contacts; upload profile photo.
- **States**: empty = n/a (employee always has own record) — if no linked employee, blocking notice; loading = card skeleton; error = validation on editable slice (e.g. invalid phone, not E.164); selected = edit mode active on own-slice fields.
- **Gating**: visible with `hr.self-service.access` *(assumed permission)*; edit own-slice requires `hr.self-service.update-own-profile` *(assumed)*.

## Data

- Owns / writes: none — this module owns no tables. May write an own-profile-correction slice to `hr_employees` **only** via hr.profiles' `UpdateOwnProfileAction`/`EmployeeService` (owning-service rule; never a direct write).
- Reads: `hr_employees`, `hr_emergency_contacts` (own record, owned by hr.profiles), scoped to `Auth::user()->employee`.
- Cross-domain writes: via owning service / events only (never another domain's tables — [[../../../../security/data-ownership]]).

## Relations

- Consumes: none.
- Feeds: own-profile edit routed through hr.profiles → may trigger an `EmployeeUpdated` effect *(assumed)*.

  > [!warning] UNVERIFIED
  > Whether the own-profile correction fires an `EmployeeUpdated` (or equivalent) event is not confirmed. Resolve against hr.profiles' event contract before build.
- Shared entity: reads `hr_employees` / `hr_emergency_contacts` (owned by hr.profiles).

[[../_module]]
