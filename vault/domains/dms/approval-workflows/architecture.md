---
domain: dms
module: approval-workflows
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
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

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `ApprovalWorkflowResource` | Approvals | #1 CRUD resource | Steps repeater; `sequential`/`parallel` type toggle. |
| `ApprovalRequestResource` | Approvals | #1 CRUD resource | "My approvals" tab; approve / reject / request-changes as row/table actions; audit-trail relation (read-only). |

See [[../../../architecture/filament-patterns]] and [[../../../architecture/ui-strategy]].

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('dms.approvals.view-any')
        && BillingService::hasModule('dms.approvals');
}
```

Custom pages state this explicitly.

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

## Events

None fired or consumed. `dms.approvals` defines no cross-domain events in v1 (`fires-events: []`, `consumes-events: []`); approver/submitter notifications are commands to `core.notifications`, not domain events. See [[../../../architecture/event-bus]] and [[unknowns]].

## Jobs & Scheduling

None specific to this module in v1. Notification delivery is handled by `core.notifications` (queued there). *(assumed — no jobs listed in source Build Manifest)*
