---
domain: hr
module: employee-feedback
feature: one-on-ones
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# 1-on-1 Meetings

## Purpose

Records of recurring 1-on-1 meetings between a manager and a direct report.

## Behavior

- Each record holds meeting date, agenda, notes, and an action-item checklist (`[{title, done}]`).
- Logged via `LogOneOnOneAction`; the reportee must report to the current manager *(assumed)*.
- Agenda and notes are visible to the **two participants only** — not HR by default.
- Rendered as a participant-scoped `OneOnOneResource` with an action-item checklist.

## Tables & Permissions

- Table: `hr_one_on_ones` (see [[../data-model]])
- Permission: `hr.feedback.one-on-one` — see [[../security]] (participant-only confidentiality)

## UI

- **Kind**: custom-page (1:1 agenda/notes, participant-scoped)
- **Page**: "1-on-1s" (`/hr/one-on-ones`)
- **Layout**: participant-scoped list of 1:1 records (meeting date, other participant) → detail with agenda, notes, and an action-item checklist (`[{title, done}]`)
- **Key interactions**: manager logs a 1:1 (`LogOneOnOneAction`; reportee must report to the current manager *(assumed)*), edits agenda/notes, toggles action-item done flags
- **States**: empty (no 1:1s → "Log your first 1-on-1") · loading (list skeleton) · error (reportee not in manager's chain) · selected (meeting detail with checklist)
- **Gating**: `hr.feedback.one-on-one`; agenda/notes visible to the two participants only — not HR by default

## Data

- Owns / writes: `hr_one_on_ones`
- Reads: `hr_employees` + manager relationship via `EmployeeService` (hr.profiles)
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none (participant-confidential; not surfaced elsewhere)
- Shared entity: `hr_employees` + manager chain (hr.profiles)

Back to [[../_module]].
