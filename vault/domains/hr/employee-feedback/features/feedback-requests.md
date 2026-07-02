---
domain: hr
module: employee-feedback
feature: feedback-requests
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feedback Requests

## Purpose

Let an employee ask a colleague or manager for feedback.

## Behavior

- `RequestFeedbackAction::run(string $fromEmployeeId): void` sends a notification asking the target person for feedback.
- No dedicated table — the request is a notification; any resulting feedback becomes an `hr_feedback` record.

## Tables & Permissions

- Table: none of its own (reuses `hr_feedback` for the eventual record)
- Permission: `hr.feedback.give` (the person responding gives feedback) — see [[../security]]

## UI

- **Kind**: simple-resource (request/collect flow — no dedicated table)
- **Page**: request action on the feedback area (`/hr/feedback` → "Request feedback")
- **Layout**: no standalone resource — a "Request feedback" action picks a target colleague/manager; the request is a notification, the eventual response becomes an `hr_feedback` record
- **Key interactions**: employee requests feedback from a colleague/manager (`RequestFeedbackAction`); the target is notified and responds by creating feedback
- **States**: empty (no requests sent) · loading (action submit) · error (invalid target) · selected (n/a — fire-and-notify)
- **Gating**: any employee may request; the responder needs `hr.feedback.give` to submit the resulting feedback

## Data

- Owns / writes: none of its own (reuses `hr_feedback` for the eventual record)
- Reads: `hr_employees` via `EmployeeService` (hr.profiles) for target selection
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none — request delivered via core.notifications; response lands in [[feedback]]
- Shared entity: `hr_employees` (hr.profiles)

Back to [[../_module]].
