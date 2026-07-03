---
domain: lms
module: mentoring
feature: mentor-directory
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Mentor Directory

Browse employees who volunteer as mentors and request a pairing.

## Behaviour

- Mentors publish a profile (`lms_mentor_profiles`): expertise, availability, `is_accepting`.
- Employees browse accepting mentors, filter by expertise, and request a pairing (`RequestMentorshipData`).
- Requests target accepting mentors only, ≠ self, no duplicate active pair.

## UI

- **Kind**: custom-page  <!-- ui-strategy row #17 gallery/directory -->
- **Page**: "Mentor Directory" (`MentorDirectoryPage`, `/lms/mentoring/directory`)
- **Layout**: card gallery (name, expertise chips, availability, "Request" button); expertise + availability filters; toggle "accepting only".
- **Key interactions**: filter by expertise; open a mentor card; "Request mentorship" → focus-area modal → creates a pending mentorship + notifies the mentor.
- **States**: empty (no accepting mentors → "No mentors are open right now") · loading (skeleton cards) · error (non-accepting / self / duplicate → validation) · selected (card expanded).
- **Gating**: `lms.mentoring.participate`.

## Data

- Owns / writes: `lms_mentor_profiles` (own profile), `lms_mentorships` (on request).
- Reads: HR employees; skills (expertise when active); core.notifications.
- Cross-domain writes: NONE.

## Relations

- Consumes: nothing.
- Feeds: a request creates a mentorship handled in [[mentorship-management]].
- Shared entity: employees (HR), skills (expertise).

## Test Checklist

### Unit
- [ ] Directory lists accepting mentors only; expertise fed from skills when active (soft-dep)

### Feature (Pest)
- [ ] Request creates pending mentorship; self-request and duplicate-active rejected (race-safe)
- [ ] Tenant isolation: directory shows own-company mentors only

### Livewire
- [ ] Directory grid renders; request action gated on the mentoring permission

## Unknowns

- Algorithmic matching vs manual browse — see [[../unknowns]].

## Related

- [[../_module|Mentoring module]] · [[mentorship-management]] · [[session-logging]]
