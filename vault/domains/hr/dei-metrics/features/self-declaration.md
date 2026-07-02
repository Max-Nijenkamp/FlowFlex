---
domain: hr
module: dei-metrics
feature: self-declaration
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Self-declaration

## Purpose

Let employees opt in and declare their own diversity attributes via HR self-service. Collection is opt-in only; no one declares on another's behalf.

## Behavior

- A DEI section in the self-service MyProfilePage offers opt-in collection with a required consent checkbox.
- Submission flows through `SubmitOwnDeiAttributesAction::run(SubmitDeiAttributesData)` — own-only, writes a consent log.
- Only jurisdiction-allowed dimensions are offered; values must be in the dimension option list.

## Tables / Permissions

- Writes `hr_dei_attributes` (value encrypted).
- Permission: `hr.dei.submit-own` (all employees).

## UI

- **Kind**: custom-page (in the `/app` employee self-service portal)
- **Page**: a DEI section on "My Profile" (`/app/my-profile`) — employee self-service, not the `/hr` staff panel
- **Layout**: opt-in DEI form listing only jurisdiction-allowed dimensions (gender, age band, ethnicity where legal, disability) as selects; a required consent checkbox gates the submit button; explanatory privacy copy above
- **Key interactions**: employee ticks consent, picks values from the allowed option lists, submits; own-record only — cannot declare for anyone else; can revisit to update or withdraw
- **States**: empty = no attributes declared yet, blank form with opt-in prompt · loading = form saving spinner · error = validation ("consent required" / value not in allowed list) · selected = previously declared dimensions pre-filled on return · consent-off = submit disabled until the consent box is ticked
- **Gating**: visible with `hr.dei.submit-own` (all employees, own record only); submission blocked without consent; only jurisdiction-allowed dimensions are offered

## Data

- Owns / writes: `hr_dei_attributes` (own module; `value` encrypted at rest) + a consent-log reference in core.privacy
- Reads: `hr_employees` (to attach attributes to the acting employee); jurisdiction config for allowed dimensions
- Cross-domain writes: consent record written via core.privacy consent API (not its tables directly) ([[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none outbound (privacy — no DEI data leaves the module); writes a consent log to core.privacy at submit time
- Shared entity: `hr_employees` (read-only, to bind the acting employee)

## Related

- [[../_module]]
- [[../features/consent-management]]
- [[../features/dei-attributes-encrypted]]
