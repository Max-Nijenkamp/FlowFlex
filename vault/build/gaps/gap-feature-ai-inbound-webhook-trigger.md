---
type: gap
severity: medium
category: feature
status: open
domain: ai
color: "#F97316"
discovered: 2026-07-03
discovered-in: ai.workflows
---

# Gap: Workflow-builder has no inbound external-webhook trigger

## Context

[[../../domains/ai/workflow-builder/features/trigger-registry|trigger-registry]] defines the trigger surface
as **the event-bus contract map + schedule triggers** — i.e. internal domain events (deal won, invoice paid,
…) and a time-based `RunScheduledWorkflowsCommand`. Scheduled/cron triggers are therefore already covered.
What is **not** present is a way for an *external* system to start a workflow by POSTing to FlowFlex.

## Problem

An inbound webhook ("Catch Hook") is, alongside schedule, one of the two universal automation entry points
Zapier/Make users expect. Without it, a tool FlowFlex doesn't own (a form vendor, a phone system, a partner
API) cannot kick off a flow — the automation can only react to things already inside FlowFlex. This caps the
"Zapier-inside" positioning of the module.

## Impact

Limits [[../../domains/ai/workflow-builder/_module|ai.workflows]] to internally-originated automation and
undercuts the migrate-off-Zapier story for any flow that starts with an external event. Package-fit —
`core.webhooks` already exists as the inbound-webhook mechanism ([[../../domains/ai/workflow-builder/features/trigger-registry|trigger-registry]]
even cites it as "same pattern").

## Proposed Solution

Add an `inbound-webhook` trigger type: `core.webhooks` exposes a per-workflow signed inbound endpoint; a
verified POST resolves the company from the endpoint's `company_id` and dispatches `RunWorkflowJob` with the
request body as the trigger payload (reusing the existing run path + loop guard). Rate-limited and
signature-verified per the webhooks security contract.

## Sources

- [Webhooks "Catch Hook" is a core Zapier trigger alongside Schedule (Zapier)](https://zapier.com/apps/webhook/integrations) (accessed 2026-07-03)
- [Schedule vs webhook are the two schedule/trigger entry points (Zapier — schedule intervals)](https://help.zapier.com/hc/en-us/articles/8496288648461-Schedule-Zaps-to-run-at-specific-intervals) (accessed 2026-07-03)
