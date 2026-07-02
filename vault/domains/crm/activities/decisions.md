---
domain: crm
module: activities
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Activities — Decisions

---

## Cursor Pagination for Timelines

Activity timelines use cursor-based pagination (not offset) per the [[../../../architecture/api-design]] rule. Rationale: activities are inserted frequently; offset pagination causes drift and duplicate/missing rows on live feeds.

## Multi-Attachment: Contact AND Deal AND Account

A single activity can be linked to all three (contact, deal, account) simultaneously. It then appears on all three timelines. The constraint is only that at least one must be provided — not that only one is allowed.

## Reminder Fires Once

The `TaskReminderCommand` runs every 15 minutes but a reminder for a given task fires only once — guarded by the `reminded_at` column. Once set, the command skips that row. This prevents spam if the command runs multiple times within the due window.

## CompleteTask: Optional Follow-up

`CompleteTaskAction` accepts an optional `$followUp` argument (another `LogActivityData`). If provided, a new task is created atomically in the same action. This allows "done → create follow-up call" as a single UX action without a separate form submit.

## TimelineQuery as Shared Scope

`TimelineQuery::for(Model $contactOrDealOrAccount)` is a shared support class, not an action, because it is called from multiple Filament view pages (Contact, Deal, Account). Keeping it in `app/Support/CRM/` avoids coupling the three resources to each other.
