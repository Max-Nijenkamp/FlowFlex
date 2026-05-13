---
type: adr
date: 2026-05-13
status: decided
color: "#F97316"
---

# Decision: Established pattern for when Filament Resources must be replaced by custom Pages

## Context

During the Phase 0–8 audit, five resources were found implemented as standard Filament CRUD (`ListRecords / CreateRecord / EditRecord`) when their UX requirements make CRUD fundamentally wrong:

- `OrgChartResource` — displayed org nodes as a data table with TextInput for employee_id
- `CopilotConversationResource` — listed AI conversations as a table with edit form
- `WorkflowResource` (AI panel) — simple form for automation workflows
- `ChatChannelResource` — only managed channel records, no message interface
- `RevenueIntelligenceResource` — read-only table of metrics

## Options Considered

1. **Keep CRUD, add extra pages** — Add a ViewRecord page with custom blade. Keeps admin CRUD available but adds complexity.

2. **Replace with custom Page entirely** — Delete resource, create `Filament\Pages\Page` subclass with purpose-built view.

3. **Keep CRUD for data management, add sibling custom Page for the UX** — Workflows and Chat Channel CRUD kept for record management; new custom pages (WorkflowBuilderPage, TeamChatPage) added alongside.

## Decision

**Rule established:**

> A Filament Resource must be replaced (or supplemented) by a custom Page when any of these are true:
> 1. The UX is inherently **interactive** (chat, canvas, tree visualization)
> 2. The resource is **read-only** in practice (analytics, reporting)
> 3. The primary interaction is **not** create/edit/delete of rows but a **workflow** (builder, chat session, org chart navigation)

**Applied this session:**
- OrgChart → deleted Resource, created `OrgChartPage` with recursive tree view
- CopilotConversation → deleted Resource, created `CopilotPage` with chat bubble UI
- RevenueIntelligence → kept Resource as data table, added `RevenueIntelligencePage` dashboard
- WorkflowResource → kept Resource for record management, added `WorkflowBuilderPage` canvas
- ChatChannel → kept Resource for channel CRUD, added `TeamChatPage` three-column chat UI

## Consequences

- New interactive features (e.g., Kanban, Gantt) should be built as custom Pages from day one, not retrofitted Resources.
- Both patterns can coexist: Resource for data management (admin/power user), custom Page for end-user UX.
- Navigation group matters: custom Page should have sort priority over companion Resource.

## Related Left Brain

- [[MOC_Foundation]] — panel patterns
- [[MOC_AI]] — Copilot, Workflow Builder
- [[MOC_HR]] — Org Chart
- [[MOC_CRM]] — Revenue Intelligence
- [[MOC_Communications]] — Team Chat
