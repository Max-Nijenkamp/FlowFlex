---
domain: lms
module: enrolments
feature: auto-enrol-on-hire
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Auto-Enrol on Hire

When HR hires an employee, enrol them into mandatory onboarding courses automatically.

## Behaviour

- `AutoEnrolOnHireListener` reacts to `EmployeeHired` (hr.profiles).
- It finds mandatory-audience `internal` courses and enrols the new employee once each.
- Idempotent: no-op when no mandatory courses exist or the employee is already enrolled (safe against event re-delivery).
- Runs `implements ShouldQueue` + `WithCompanyContext`; the event's `company_id` scalar scopes every write.
- Due dates are applied per the mandatory course's policy *(assumed)*.

## UI

- **Kind**: background  <!-- queued listener, no screen -->
- **Trigger**: `EmployeeHired` event → queued `AutoEnrolOnHireListener`. Its results surface in the [[enrolment-management|Enrolments resource]] compliance tab; no dedicated page.

## Data

- Owns / writes: `lms_enrolments` (its own table).
- Reads: `EmployeeHired` payload (`company_id`, employee id — scalars, not models); mandatory course list (courses).
- Cross-domain writes: NONE — reacts to an HR event and writes only LMS enrolment rows, never hr tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `EmployeeHired` from `hr.profiles` → enrol into mandatory courses.
- Feeds: enrolments then behave normally (progress, completion side effects).
- Shared entity: employee = a `users` record (read via the event id; HR owns the profile).

## Test Checklist

### Unit
- [ ] Mandatory-course set resolution for a new hire (role/department mapping)

### Feature (Pest)
- [ ] HR hire event listener (`ShouldQueue` + `WithCompanyContext`) enrols into mandatory courses once; replayed event does not duplicate (duplicate-active guard)
- [ ] Tenant isolation: event company_id scalar drives scoping

### Livewire
- (none -- background listener)

## Unknowns

- Which courses count as "mandatory onboarding" — a flag on the course, a role→course map, or config — is unmodelled. See [[../unknowns]].

## Related

- [[../_module|Enrolments module]] · [[../../../hr/onboarding/_module|HR Onboarding]] · [[../../../../architecture/event-bus|Event Bus]]
