---
domain: hr
module: employee-feedback
feature: feedback
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feedback Records

## Purpose

Lightweight feedback from one employee to another: praise, constructive, or coaching note.

## Behavior

- A feedback record carries from, to, type, message, visibility, optional related goal, and tags.
- Types: **praise** (public recognition), **constructive** (private), **coaching note** (manager-only visibility).
- Visibility is forced by type — constructive is always private; praise is public-capable; coaching notes follow the manager chain.
- Can link to a goal or performance-review cycle.
- Self-feedback (from == to) is rejected.
- Recipient is notified on creation. Public praise also lands on the [[recognition-feed|recognition feed]].
- Tagged via `spatie/laravel-tags` (skill / value demonstrated).

## Tables & Permissions

- Table: `hr_feedback` (see [[../data-model]])
- Permissions: `hr.feedback.give`, `hr.feedback.view-own`, `hr.feedback.view-any` (HR) — see [[../security]]

## UI

- **Kind**: simple-resource
- **Page**: "Feedback" (`/hr/feedback`)
- **Layout**: Filament table scoped by visibility (from, to, type badge, message, tags) + give-feedback form (type, message, visibility forced by type, optional related goal, tags)
- **Key interactions**: employee gives feedback (praise/constructive/coaching-note) to a colleague; visibility auto-forced by type; can link a goal/cycle and tag skills/values
- **States**: empty (no feedback → "No feedback yet") · loading (table skeleton) · error (self-feedback from==to rejected) · selected (feedback detail respecting visibility)
- **Gating**: give requires `hr.feedback.give`; view own with `hr.feedback.view-own`; HR sees all with `hr.feedback.view-any`

## Data

- Owns / writes: `hr_feedback`
- Reads: `hr_review_goals` (optional `related_goal_id`) from hr.performance; `hr_employees` (hr.profiles)
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: performance-review context signals from `hr.performance` (soft — feedback links to a goal/cycle; standalone otherwise)
- Feeds: public praise → [[recognition-feed]]; recipient notification via core.notifications
- Shared entity: `hr_employees` (hr.profiles), `hr_review_goals` (hr.performance)

Back to [[../_module]].
