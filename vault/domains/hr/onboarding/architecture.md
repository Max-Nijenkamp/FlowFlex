---
domain: hr
module: onboarding
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Onboarding — Architecture

Interface→Service per [[../../../architecture/patterns/interface-service]]:
`OnboardingServiceInterface` → `OnboardingService`.

## Services & Actions

| Method | Signature | Behavior |
|---|---|---|
| `startPlan` | `startPlan(string $companyId, string $employeeId, ?string $templateId = null): OnboardingPlanData` | Picks dept template → company default → no-op when none. Materializes plan tasks from template tasks. Queues welcome mail. |
| `completeTask` | `completeTask(CompleteTaskData $data): void` | Marks a plan task complete/skipped; auto-sets plan `completed_at` when last task closed. |
| `progress` | `progress(string $planId): float` | Returns % of tasks complete/skipped. |

## Listener

`StartOnboardingFlowListener` — consumes `EmployeeHired`, queued, `WithCompanyContext`. Delegates to `OnboardingService::startPlan`. Behavior per [[../../../architecture/event-bus]] contract (default plan if template exists, else no-op, no error).

## Scheduled Work

`SendMilestoneCheckInsCommand` — daily 08:00, `notifications` queue. Sends 30/60/90d reminders relative to `started_at`, once per milestone. See [[../../../infrastructure/queue-horizon]] and [[../../../infrastructure/mail]].

## Flow: Plan Generation on Hire

```mermaid
flowchart TD
    A[EmployeeHired event] --> B[StartOnboardingFlowListener queued]
    B --> C{Dept template?}
    C -->|yes| D[use dept template]
    C -->|no| E{Company default?}
    E -->|yes| F[use default template]
    E -->|no| G[no-op, no error]
    D --> H[create hr_onboarding_plan]
    F --> H
    H --> I[materialize plan_tasks from template tasks]
    I --> J[queue WelcomeMail]
```

## Flow: Task Completion

```mermaid
flowchart TD
    A[completeTask CompleteTaskData] --> B[set plan_task status + completed_by/at]
    B --> C{all tasks complete or skipped?}
    C -->|yes| D[set plan.completed_at]
    C -->|no| E[plan stays active]
```
