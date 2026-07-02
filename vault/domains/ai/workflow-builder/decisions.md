---
domain: ai
module: workflow-builder
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Workflow Builder — Decisions

---

## No LLM — this is deterministic orchestration, "AI" only by placement

Workflow Builder uses **no model**. It is a deterministic trigger → condition → action engine. It lives in the `/ai` domain for product grouping ("AI & Automation"), but has zero dependency on `ai.config`/`LlmGateway`. Keeping it LLM-free means runs are reproducible and auditable — a hard requirement for automation that moves money or creates records.

---

## Universal event listener, like core.webhooks

A single `WorkflowTriggerListener` subscribes to **all** contracted domain events rather than each domain wiring workflow hooks. Same pattern as [[../../core/webhooks/_module|core.webhooks]]: one listener, registry-driven matching. This keeps the trigger surface = the event-bus map, automatically, with no per-domain glue.

---

## Actions execute through the owning module's service — never raw writes

Every action is an `ActionDefinition` that calls the owning module's service (create task, notify, webhook, update record). Workflow Builder writes only its own two tables; all cross-domain effects go through the authorised service path, respecting that module's validation, permissions, and `CompanyScope` ([[../../../security/data-ownership]]).

---

## Validate the graph at save, not at run

`WorkflowGraphValidator` rejects unknown events, inactive-module actions, cycles, and orphan nodes **at save time**. A workflow that passes save is structurally sound; run-time failures are then only genuine action errors (handled by per-action error policy), not authoring mistakes.

---

## Per-action error policy: retry / stop / continue

Each action node carries an error policy — `retry` (3× with backoff), `stop` (halt → `failed`/`partial`), or `continue` (log + proceed). This gives the author explicit control over partial-failure behaviour instead of an all-or-nothing run.

---

## Loop guard to prevent cascades

Workflow-produced events are tagged (*(assumed)* system-actor flag) so they don't re-trigger workflows (depth 1). Prevents infinite "action → event → workflow → action" cascades. The exact tagging mechanism is unconfirmed — see [[unknowns]].

---

## v1 builder may be list-based, visual canvas later

The flow editor is specced as a #9-style node editor, but v1 may ship as a **list-based builder** (ordered condition/action steps) with a visual drag-canvas deferred. Reduces front-end cost without changing the underlying graph model. *(assumed)* — see [[unknowns]].
