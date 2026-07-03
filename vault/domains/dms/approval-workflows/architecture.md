---
domain: dms
module: approval-workflows
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Approval Workflows — Architecture

## Services & Actions

Interface→Service binding (`DmsServiceProvider`): `ApprovalServiceInterface` → `ApprovalService`.

| Class | Type | Responsibility |
|---|---|---|
| `ApprovalService::submit(SubmitForApprovalData): ApprovalRequest` | service method | Create the request, transition `pending → in_review`, lock the document, notify the first approver(s). Throws `OpenRequestExistsException` if the document already has an open request. |
| `ApprovalService::act(ApprovalActionData): ApprovalRequest` | service method | Validate that the actor is a current-step approver and **≠ submitter**; record a `dms_approval_actions` row. **Sequential**: advance `current_step`. **Parallel**: complete when all approvers have acted approve. One reject rejects; request-changes returns to `pending`. |
| `ApprovalService::pendingFor(User $user): Collection` | service method | The "my approvals" queue — requests where this user is a current-step approver. |

## Filament Artifacts

**Nav group:** Approvals

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `ApprovalWorkflowResource` | #1 CRUD resource | tweaks: inline-relation-repeater (steps) | steps repeater; `sequential`/`parallel` type toggle; list filters: type |
| `ApprovalRequestResource` | #1 CRUD resource | tweaks: state-badge-column, custom-header-actions (approve / reject / request-changes), relation-manager-timeline (audit trail, read-only) | "My approvals" tab; reject + request-changes open a comment modal |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('dms.approvals.view-any') && BillingService::hasModule('dms.approvals')`
per [[../../../architecture/filament-patterns]] #1. Custom pages MUST state it explicitly — Filament does not
auto-gate them. Beyond the resource gate, `ApprovalService::act()` enforces a second, runtime gate (current-step
approver only, and submitter ≠ approver) — see [[security#Action Authorization (second gate)]].

> [!warning] UNVERIFIED
> The access contract gates on `dms.approvals.view-any`, but the source `## Permissions` list only names `dms.approvals.manage-workflows`, `dms.approvals.submit`, and `dms.approvals.act`. `view-any` is not in the permission list — see [[unknowns]].

## State Machine

`ApprovalRequest` uses `spatie/laravel-model-states`. States: `Pending`, `InReview`, `Approved`, `Rejected` under `app/States/DMS/ApprovalRequest/`.

| State | Transitions to | Triggered by | Side effects |
|---|---|---|---|
| `pending` | `in_review` | submit | Document locked; first approver(s) notified. |
| `in_review` | `approved` | last required approval (all steps in sequential / all approvers in parallel) | Document unlocked; submitter notified; `completed_at` set. |
| `in_review` | `rejected` | any reject (with required comment) | Document unlocked; rejection reason sent to submitter; `completed_at` set. |
| `in_review` | `pending` (request changes) | request-changes | Document unlocked for edit; resubmit restarts the chain from `current_step = 0`. |

Every transition is audited — an action row is written to `dms_approval_actions`, and the request state change is captured by the activity log.

> [!note]
> Re-submit after a **rejection** creates a *new* `dms_approval_requests` row (the original is kept for history). Request-changes, by contrast, reuses the same request and moves it back to `pending`.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Workflow CRUD (`ApprovalWorkflowResource` form) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Submit / approve / reject / request-changes (status transition + `current_step` advance) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the request, re-read `current_step`/status, validate, write per [[../../../architecture/patterns/states]] — prevents two approvers double-advancing a parallel/sequential chain |
| Document lock/unlock during review | Document locks | Commanded to [[../version-control/_module\|dms.versions]]'s checkout mechanism (`dms_document_locks`); this module holds no lock row itself — the document-locks tier lives in the versions module |
| Audit action rows (`dms_approval_actions`) | n/a | Append-only inserts — never updated or deleted, so no concurrent-edit surface |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Events

None fired or consumed. `dms.approvals` defines no cross-domain events in v1 (`fires-events: []`, `consumes-events: []`); approver/submitter notifications are commands to `core.notifications`, not domain events. See [[../../../architecture/event-bus]] and [[unknowns]].

## Jobs & Scheduling

None specific to this module in v1. Notification delivery is handled by `core.notifications` (queued there). *(assumed — no jobs listed in source Build Manifest)*
