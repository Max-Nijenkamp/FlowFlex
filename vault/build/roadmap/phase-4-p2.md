---
type: roadmap-phase
color: "#F97316"
updated: 2026-07-03
---

# Phase 4 — p2 domains

Projects, Support, Communications, DMS.

**31 modules · 83 features.** Work top-to-bottom; within a domain, modules are ordered fewest-dependencies-first. Tick a feature only after BOTH gates pass: AI gate (spec Test Checklist covered by green Pest tests + `/flowflex:verify`) AND your hand check.

## communications

### Comms Analytics — `communications.comms-analytics`

Build: `/flowflex:start communications.comms-analytics` · Done: `/flowflex:done communications.comms-analytics` · Spec: [[../../domains/communications/comms-analytics/_module|hub]] · Hard deps: comms.inbox, core.billing, core.rbac

- [ ] **Agent Performance** ([[../../domains/communications/comms-analytics/features/agent-performance|spec]]) — hand-check: open `AgentPerformanceWidget` on `CommsAnalyticsDashboard` — Analytics nav group.; sort columns; date/channel filter recomputes.
- [ ] **Channel Mix & Volume** ([[../../domains/communications/comms-analytics/features/channel-mix|spec]]) — hand-check: open `ChannelVolumeWidget` + `ChannelMixWidget` on `CommsAnalyticsDashboard` — Analytics nav group (apex charts).; date/channel filter; hover buckets for detail; polls 60s.
- [ ] **Response-time Metrics** ([[../../domains/communications/comms-analytics/features/response-time-metrics|spec]]) — hand-check: open `ResponseTimeWidget` on `CommsAnalyticsDashboard` (`/comms/analytics`) — Analytics nav group.; change date range / channel filter → widget recomputes (polls 60s).
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Automations — `communications.automations`

Build: `/flowflex:start communications.automations` · Done: `/flowflex:done communications.automations` · Spec: [[../../domains/communications/automations/_module|hub]] · Hard deps: comms.inbox, core.billing, core.rbac, core.settings

- [ ] **Auto-reply Rules** ([[../../domains/communications/automations/features/auto-reply-rules|spec]]) — hand-check: build conditions → pick reply template → activate; reorder among all rules.
- [ ] **Chatbot Flows** ([[../../domains/communications/automations/features/chatbot-flows|spec]]) — hand-check: add nodes → wire options to next/action → validate → activate (one active per channel *(assumed)*).
- [ ] **Routing Rules** ([[../../domains/communications/automations/features/routing-rules|spec]]) — hand-check: build conditions → pick action(s) → order rules → toggle stop-on-match.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Broadcast — `communications.broadcast`

Build: `/flowflex:start communications.broadcast` · Done: `/flowflex:done communications.broadcast` · Spec: [[../../domains/communications/broadcast/_module|hub]] · Hard deps: comms.inbox, core.billing, core.rbac, foundation.queues

- [ ] **Compose & Schedule** ([[../../domains/communications/broadcast/features/compose-schedule|spec]]) — hand-check: open `BroadcastResource` (`/comms/broadcast`) — Broadcast nav group.; build audience → compose → preview → "Send now" / "Schedule"; state badge tracks lifecycle.
- [ ] **Delivery Tracking** ([[../../domains/communications/broadcast/features/delivery-tracking|spec]]) — hand-check: open `BroadcastStatsWidget` (#6) on the `BroadcastResource` view page — Broadcast nav group.; open a broadcast → live funnel; filter recipients by status.
- [ ] **Recipient Materialisation** ([[../../domains/communications/broadcast/features/recipient-materialisation|spec]]) — hand-check: background — trigger it (: `BroadcastService::schedule` (on send/schedule). No dedicated screen; recipient count + ), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Email Channel — `communications.email-channel`

Build: `/flowflex:start communications.email-channel` · Done: `/flowflex:done communications.email-channel` · Spec: [[../../domains/communications/email-channel/_module|hub]] · Hard deps: comms.inbox, core.billing, core.rbac, foundation.queues

- [ ] **Inbound Parsing** ([[../../domains/communications/email-channel/features/inbound-parsing|spec]]) — hand-check: background — trigger it (: `POST /webhooks/comms/email/inbound` (guest, signature-verified). No screen — messages s), then check the visible result named in the spec
- [ ] **Outbound Threading** ([[../../domains/communications/email-channel/features/outbound-threading|spec]]) — hand-check: type reply → send → driver sets headers + from + signature.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Internal Messaging — `communications.internal-messaging`

Build: `/flowflex:start communications.internal-messaging` · Done: `/flowflex:done communications.internal-messaging` · Spec: [[../../domains/communications/internal-messaging/_module|hub]] · Hard deps: core.billing, core.rbac, core.files, core.notifications

- [ ] **Channels & DMs** ([[../../domains/communications/internal-messaging/features/channels-dms|spec]]) — hand-check: create channel (name, type, members) · join public · invite to private · click user → open DM.
- [ ] **Realtime Messaging** ([[../../domains/communications/internal-messaging/features/realtime-messaging|spec]]) — hand-check: open `InternalMessagingPage` (`/comms/messaging`) — Messaging nav group, ui-strategy row #8.; type (whisper typing) → send → optimistic append + broadcast; scroll up → load older (cursor); focus channel → mark read.
- [ ] **Threads, Reactions & Mentions** ([[../../domains/communications/internal-messaging/features/threads-reactions|spec]]) — hand-check: hover message → react/reply; @type → member autocomplete; search → jump to result (member channels only).
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Shared Inbox — `communications.shared-inbox`

Build: `/flowflex:start communications.shared-inbox` · Done: `/flowflex:done communications.shared-inbox` · Spec: [[../../domains/communications/shared-inbox/_module|hub]] · Hard deps: core.billing, core.rbac, core.files, foundation.queues

- [ ] **Channel Driver Registry** ([[../../domains/communications/shared-inbox/features/channel-driver-registry|spec]]) — hand-check: background — trigger it (: driver registration at boot (service providers). No screen; channel management/config li), then check the visible result named in the spec
- [ ] **Collision Detection** ([[../../domains/communications/shared-inbox/features/collision-detection|spec]]) — hand-check: focus composer → broadcast whisper; receive whisper → show banner; send/leave → clear.
- [ ] **Snooze & Reopen** ([[../../domains/communications/shared-inbox/features/snooze-reopen|spec]]) — hand-check: click snooze → pick "until" → conversation hidden; auto-returns on time or on inbound.
- [ ] **Unified Conversation View** ([[../../domains/communications/shared-inbox/features/unified-conversation-view|spec]]) — hand-check: open "Shared Inbox" (`/comms/inbox`) — ui-strategy row #8.; select conversation → load thread; type reply → `InboxService::send` via driver → optimistic append; assign / set-status / snooze 
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### SMS Channel — `communications.sms-channel`

Build: `/flowflex:start communications.sms-channel` · Done: `/flowflex:done communications.sms-channel` · Spec: [[../../domains/communications/sms-channel/_module|hub]] · Hard deps: comms.inbox, core.billing, core.rbac, foundation.queues

- [ ] **Cost Tracking** ([[../../domains/communications/sms-channel/features/cost-tracking|spec]]) — hand-check: background — trigger it (: provider status callback (webhook).), then check the visible result named in the spec
- [ ] **Inbound & Opt-out** ([[../../domains/communications/sms-channel/features/inbound-optout|spec]]) — hand-check: background — trigger it (: `POST /webhooks/comms/sms` (guest, signature-verified). Inbound messages surface in the ), then check the visible result named in the spec
- [ ] **Outbound Send** ([[../../domains/communications/sms-channel/features/outbound-send|spec]]) — hand-check: type → counter updates → send; opted-out recipient → blocked with a message.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### WhatsApp — `communications.whatsapp`

Build: `/flowflex:start communications.whatsapp` · Done: `/flowflex:done communications.whatsapp` · Spec: [[../../domains/communications/whatsapp/_module|hub]] · Hard deps: comms.inbox, core.billing, core.rbac, foundation.queues

- [ ] **Inbound Webhook** ([[../../domains/communications/whatsapp/features/inbound-webhook|spec]]) — hand-check: background — trigger it (: `POST /webhooks/whatsapp` (guest, signature-verified). No screen. Inbound messages surfa), then check the visible result named in the spec
- [ ] **Template Management** ([[../../domains/communications/whatsapp/features/template-management|spec]]) — hand-check: open `WhatsAppTemplateResource` (`/comms/whatsapp/templates`) — Settings nav group.; create draft → "Submit for approval" row action → badge tracks status; rejected shows reason.
- [ ] **Window Sending (24h rule)** ([[../../domains/communications/whatsapp/features/window-sending|spec]]) — hand-check: type + send inside window; outside → pick template → fill variables → send.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## dms

### Document Library — `dms.document-library`

Build: `/flowflex:start dms.document-library` · Done: `/flowflex:done dms.document-library` · Spec: [[../../domains/dms/document-library/_module|hub]] · Hard deps: core.billing, core.rbac, core.files

- [ ] **Document Search** ([[../../domains/dms/document-library/features/document-search|spec]]) — hand-check: open within "Document Library" (`/dms/library?q=`).; type → debounced query → results; click result → open viewer; clear → back to folder grid.
- [ ] **Document Upload** ([[../../domains/dms/document-library/features/document-upload|spec]]) — hand-check: open within "Document Library" (`/dms/library`).; drag file → optimistic progress row → real row on complete; disallowed type → inline rejection toast before upload.
- [ ] **Document Viewer** ([[../../domains/dms/document-library/features/document-viewer|spec]]) — hand-check: open "Document Viewer" (`/dms/library/{document}`).; scroll/zoom preview; download button (signed URL); favourite toggle; move/copy action; open version history.
- [ ] **Folder Access Control** ([[../../domains/dms/document-library/features/folder-access-control|spec]]) — hand-check: open `FolderResource` form (`/dms/library/folders/{folder}/edit`).; toggle restricted → reveal role/user picker; save re-resolves the accessible set.
- [ ] **Folder Browser** ([[../../domains/dms/document-library/features/folder-browser|spec]]) — hand-check: open "Document Library" (`/dms/library`).; click folder → load grid; drag file → optimistic upload row + progress → replace with real row on complete; click document → open 
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Document Templates — `dms.templates`

Build: `/flowflex:start dms.templates` · Done: `/flowflex:done dms.templates` · Spec: [[../../domains/dms/templates/_module|hub]] · Hard deps: dms.library, core.billing, core.rbac

- [ ] **Generate From Template** ([[../../domains/dms/templates/features/generate-from-template|spec]]) — hand-check: open `GenerateFromTemplatePage` — "Templates" nav group (`/dms/templates/generate`).; choose source → whitelisted fields auto-fill, remaining fields shown as manual inputs; incomplete fields → step blocked; generate 
- [ ] **Merge Source Registry** ([[../../domains/dms/templates/features/merge-source-registry|spec]]) — hand-check: background — trigger it (: HR / CRM service providers call `MergeSourceRegistry::register` at boot; the registry is), then check the visible result named in the spec
- [ ] **Template Editor** ([[../../domains/dms/templates/features/template-editor|spec]]) — hand-check: open `DocumentTemplateResource` — "Templates" nav group (`/dms/templates`).; pick a merge field from the insert menu → placeholder dropped at cursor; save → purify + validate placeholders; open a system temp
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Version Control — `dms.version-control`

Build: `/flowflex:start dms.version-control` · Done: `/flowflex:done dms.version-control` · Spec: [[../../domains/dms/version-control/_module|hub]] · Hard deps: dms.library, core.billing, core.rbac

- [ ] **Document Locking** ([[../../domains/dms/version-control/features/document-locking|spec]]) — hand-check: open "Document Viewer" (`/dms/library` viewer) — lock / unlock header action + lock badge.; click lock → badge appears, upload enabled for you; another user sees the badge + a disabled upload; admin clicks force-unlock → c
- [ ] **Restore Version** ([[../../domains/dms/version-control/features/restore-version|spec]]) — hand-check: open "Document Viewer" (`/dms/library` viewer) — "Restore" row action in the version-history list.; click restore → confirm modal → new current version created → success toast + history row appended (never removed).
- [ ] **Upload Version** ([[../../domains/dms/version-control/features/upload-version|spec]]) — hand-check: open "Document Viewer" (`/dms/library` viewer) — "Upload new version" header action.; click action → modal → drop file → progress → success toast + history row appended; disallowed type/oversize → inline rejection be
- [ ] **Version History** ([[../../domains/dms/version-control/features/version-history|spec]]) — hand-check: open "Document Viewer" (`/dms/library` viewer) — version-history relation manager / panel.; click download → signed URL → file; click restore → confirm → new current version (see restore-version).
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Approval Workflows — `dms.approval-workflows`

Build: `/flowflex:start dms.approval-workflows` · Done: `/flowflex:done dms.approval-workflows` · Spec: [[../../domains/dms/approval-workflows/_module|hub]] · Hard deps: dms.library, core.billing, core.rbac, core.notifications

- [ ] **Approval Audit Trail** ([[../../domains/dms/approval-workflows/features/approval-audit-trail|spec]]) — hand-check: open an audit-trail relation on `ApprovalRequestResource` (Approvals nav group, `/dms/approval-requests`).; view only; no create/edit/delete (append-only, written by the service).
- [ ] **Approver Actions** ([[../../domains/dms/approval-workflows/features/approver-actions|spec]]) — hand-check: open `ApprovalRequestResource` with a "My approvals" tab (Approvals nav group, `/dms/approval-requests`).; click approve → optimistic advance; reject/changes → comment modal → transition + notification; wrong-step / self-approval → block
- [ ] **Submit for Approval** ([[../../domains/dms/approval-workflows/features/submit-for-approval|spec]]) — hand-check: open a "Submit for approval" row/create action within `ApprovalRequestResource` (Approvals nav group, `/dms/approva; submit → request created, document locks, confirmation toast; duplicate open request → inline error from `OpenRequestExistsExcepti
- [ ] **Workflow Builder** ([[../../domains/dms/approval-workflows/features/workflow-builder|spec]]) — hand-check: open `ApprovalWorkflowResource` under the "Approvals" nav group (`/dms/approval-workflows`).; add/reorder/remove steps; switching to `parallel` de-emphasises step ordering (all approve). Delete = soft delete.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Retention Policies — `dms.retention-policies`

Build: `/flowflex:start dms.retention-policies` · Done: `/flowflex:done dms.retention-policies` · Spec: [[../../domains/dms/retention-policies/_module|hub]] · Hard deps: dms.library, core.billing, core.rbac, core.notifications, foundation.queues

- [ ] **Legal Hold** ([[../../domains/dms/retention-policies/features/legal-hold|spec]]) — hand-check: open "Legal Holds" (`/dms/legal-holds`), nav group Settings.
- [ ] **Retention Audit Log** ([[../../domains/dms/retention-policies/features/retention-audit-log|spec]]) — hand-check: open "Retention Log" (`/dms/retention-log`), nav group Settings.
- [ ] **Retention Policy** ([[../../domains/dms/retention-policies/features/retention-policy|spec]]) — hand-check: open "Retention Policies" (`/dms/retention-policies`), nav group Settings.
- [ ] **Retention Run** ([[../../domains/dms/retention-policies/features/retention-run|spec]]) — hand-check: background — trigger it (ed by the scheduler: `ProcessRetentionCommand` at 03:00 daily. Results are observable only), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## projects

### Projects — `projects.projects`

Build: `/flowflex:start projects.projects` · Done: `/flowflex:done projects.projects` · Spec: [[../../domains/projects/projects/_module|hub]] · Hard deps: core.billing, core.rbac

- [ ] **Membership & Visibility** ([[../../domains/projects/projects/features/project-membership|spec]]) — hand-check: open "Members" tab / relation manager under `ProjectResource` detail.; add member → optimistic row; change role → inline select; remove → confirm.
- [ ] **Project Record & Health** ([[../../domains/projects/projects/features/project-record|spec]]) — hand-check: open `ProjectResource` at `/app/projects/projects`; detail view `/app/projects/projects/{id}`.; create/edit form; status transition actions gated by the machine; health chip colour-coded.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Kanban Board — `projects.kanban`

Build: `/flowflex:start projects.kanban` · Done: `/flowflex:done projects.kanban` · Spec: [[../../domains/projects/kanban/_module|hub]] · Hard deps: projects.tasks, core.billing, core.rbac

- [ ] **Board View & Drag-Move** ([[../../domains/projects/kanban/features/board-view|spec]]) — hand-check: open `KanbanBoardPage` at `/app/projects/kanban` (nav group Projects).; drag card → confirm/optimistic move → `MoveTask` → broadcast; quick-add in column; filter chips.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### OKRs — `projects.okrs`

Build: `/flowflex:start projects.okrs` · Done: `/flowflex:done projects.okrs` · Spec: [[../../domains/projects/okrs/_module|hub]] · Hard deps: core.billing, core.rbac, core.notifications

- [ ] **Check-ins & Dashboard** ([[../../domains/projects/okrs/features/checkins-dashboard|spec]]) — hand-check: open `OkrDashboardPage` at `/app/projects/okrs/dashboard` (nav group OKRs); check-in is a KR row action.; quarter switch → recompute view; check-in modal (value + notes); click objective → detail.
- [ ] **Objectives & Key Results** ([[../../domains/projects/okrs/features/objectives-key-results|spec]]) — hand-check: open `ObjectiveResource` at `/app/projects/okrs` (nav group OKRs).; create objective + KRs; reparent (cycle/depth validated); progress bars reflect roll-up.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Resource Allocation — `projects.resource-allocation`

Build: `/flowflex:start projects.resource-allocation` · Done: `/flowflex:done projects.resource-allocation` · Spec: [[../../domains/projects/resource-allocation/_module|hub]] · Hard deps: projects.projects, core.billing, core.rbac

- [ ] **Allocation Record & Conflicts** ([[../../domains/projects/resource-allocation/features/allocation-record|spec]]) — hand-check: open `ResourceAllocationResource` at `/app/projects/resources` (nav group Settings).; create/edit form; over-allocation badge with tooltip listing conflicting allocations.
- [ ] **Capacity Timeline** ([[../../domains/projects/resource-allocation/features/capacity-timeline|spec]]) — hand-check: open `AllocationTimelinePage` at `/app/projects/resources/timeline` (nav group Settings).; hover bar → allocation detail; date-range scrub; toggle plan-vs-actual overlay.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Sprints — `projects.sprints`

Build: `/flowflex:start projects.sprints` · Done: `/flowflex:done projects.sprints` · Spec: [[../../domains/projects/sprints/_module|hub]] · Hard deps: projects.tasks, core.billing, core.rbac

- [ ] **Burndown & Velocity** ([[../../domains/projects/sprints/features/burndown-velocity|spec]]) — hand-check: open `BurndownChartWidget` on `SprintResource` view; velocity shown on the sprint list / dashboard.; hover data points → tooltip; date-range implicit to the sprint.
- [ ] **Sprint Lifecycle & Backlog** ([[../../domains/projects/sprints/features/sprint-lifecycle|spec]]) — hand-check: open `SprintResource` at `/app/projects/sprints`; `SprintBoardPage` at `/app/projects/sprints/board`.; start (validates one-active) → confirm; drag backlog task in; complete → modal choosing incomplete-task destination.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Time Tracking — `projects.time-tracking`

Build: `/flowflex:start projects.time-tracking` · Done: `/flowflex:done projects.time-tracking` · Spec: [[../../domains/projects/time-tracking/_module|hub]] · Hard deps: projects.tasks, core.billing, core.rbac

- [ ] **Entry & Timer** ([[../../domains/projects/time-tracking/features/time-entry-timer|spec]]) — hand-check: open `TimeEntryResource` at `/app/projects/time`; timer widget embedded on task detail + Kanban card.; start timer (blocks if one running → toast); stop → entry created; manual add form.
- [ ] **Report & CSV Export** ([[../../domains/projects/time-tracking/features/time-report-export|spec]]) — hand-check: open `ProjectTimeReportPage` at `/app/projects/time/report` (nav group Time).; filter → recompute; export CSV (throttled) → download.
- [ ] **Timesheet & Approval** ([[../../domains/projects/time-tracking/features/timesheet-approval|spec]]) — hand-check: open `TimesheetPage` at `/app/projects/time/timesheet` (nav group Time).; navigate weeks; edit a cell (opens entry); approve week → confirm → all entries stamped.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Workload — `projects.workload`

Build: `/flowflex:start projects.workload` · Done: `/flowflex:done projects.workload` · Spec: [[../../domains/projects/workload/_module|hub]] · Hard deps: projects.tasks, core.billing, core.rbac

- [ ] **Workload Heat-map** ([[../../domains/projects/workload/features/workload-heatmap|spec]]) — hand-check: open `WorkloadPage` at `/app/projects/workload` (nav group Projects).; drag task between cells → reassign/reschedule; hover cell → task list tooltip; toggle day/week granularity.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Gantt Chart — `projects.gantt`

Build: `/flowflex:start projects.gantt` · Done: `/flowflex:done projects.gantt` · Spec: [[../../domains/projects/gantt/_module|hub]] · Hard deps: projects.tasks, projects.milestones, core.billing, core.rbac

- [ ] **Timeline & Critical Path** ([[../../domains/projects/gantt/features/timeline-view|spec]]) — hand-check: open `GanttChartPage` at `/app/projects/gantt` (nav group Projects).; drag bar → reschedule; drag edge → resize; hover → tooltip; critical path highlighted.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Milestones — `projects.milestones`

Build: `/flowflex:start projects.milestones` · Done: `/flowflex:done projects.milestones` · Spec: [[../../domains/projects/milestones/_module|hub]] · Hard deps: projects.projects, projects.tasks, core.billing, core.rbac, core.notifications

- [ ] **Overdue & Reminders** ([[../../domains/projects/milestones/features/milestone-reminders|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Milestone Tracking & Progress** ([[../../domains/projects/milestones/features/milestone-tracking|spec]]) — hand-check: open `MilestoneResource` at `/app/projects/milestones`; `MilestoneTimelineWidget` on the project detail.; create + link tasks (multi-select same project); achieve action; progress bar auto-updates.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Tasks — `projects.tasks`

Build: `/flowflex:start projects.tasks` · Done: `/flowflex:done projects.tasks` · Spec: [[../../domains/projects/tasks/_module|hub]] · Hard deps: projects.projects, core.billing, core.rbac, core.notifications, core.files

- [ ] **Comments & @mentions** ([[../../domains/projects/tasks/features/comments-mentions|spec]]) — hand-check: open "Comments" tab under the task detail view.; post comment → optimistic append + broadcast; @mention picker; attach file → upload progress.
- [ ] **My Tasks** ([[../../domains/projects/tasks/features/my-tasks|spec]]) — hand-check: open `MyTasksPage` at `/app/projects/my-tasks` (nav group Tasks).; quick status change inline; click → task detail; collapse groups.
- [ ] **Sub-tasks & Dependencies** ([[../../domains/projects/tasks/features/subtasks-dependencies|spec]]) — hand-check: open "Sub-tasks" and "Dependencies" tabs under the task detail.; add sub-task inline → optimistic row; add dependency → task picker → cycle validated server-side → error toast on cycle.
- [ ] **Task CRUD & Status** ([[../../domains/projects/tasks/features/task-crud|spec]]) — hand-check: open `TaskResource` at `/app/projects/tasks`; detail `/app/projects/tasks/{id}`.; create modal/form; status transition actions gated by the machine; inline assignee/priority edit.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Project Templates — `projects.templates`

Build: `/flowflex:start projects.templates` · Done: `/flowflex:done projects.templates` · Spec: [[../../domains/projects/templates/_module|hub]] · Hard deps: projects.projects, projects.tasks, projects.milestones, core.billing, core.rbac

- [ ] **Instantiate from Template** ([[../../domains/projects/templates/features/instantiate-project|spec]]) — hand-check: open `CreateProjectFromTemplatePage` at `/app/projects/templates/create` (nav group Settings).; step navigation; live due-date preview from start date; confirm → single-transaction instantiate → deep-link to the new project.
- [ ] **Template Authoring** ([[../../domains/projects/templates/features/template-authoring|spec]]) — hand-check: open `ProjectTemplateResource` at `/app/projects/templates` (nav group Settings).; add/reorder sections + tasks; save-as-template action on a project; duplicate a system template.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## support

### Knowledge Base — `support.knowledge-base`

Build: `/flowflex:start support.knowledge-base` · Done: `/flowflex:done support.knowledge-base` · Spec: [[../../domains/support/knowledge-base/_module|hub]] · Hard deps: core.billing, core.rbac

- [ ] **Article Authoring** ([[../../domains/support/knowledge-base/features/article-authoring|spec]]) — hand-check: open `KbArticleResource` (`/support/kb/articles`), `KbCategoryResource` (`/support/kb/categories`).; edit body (purified on save); publish/unpublish action; reorder categories (tree order).
- [ ] **Public Help Centre** ([[../../domains/support/knowledge-base/features/public-help-centre|spec]]) — hand-check: open Help Centre (`/help/{company}` index, `/help/{company}/{category}/{slug}` article) — `HelpCentreController` + ; search-as-you-type (published-only); thumbs up/down (optimistic, rate-limited); category drill-down.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Canned Responses — `support.canned-responses`

Build: `/flowflex:start support.canned-responses` · Done: `/flowflex:done support.canned-responses` · Spec: [[../../domains/support/canned-responses/_module|hub]] · Hard deps: support.tickets, core.billing, core.rbac

- [ ] **Composer Insertion** ([[../../domains/support/canned-responses/features/composer-insertion|spec]]) — hand-check: open action within `TicketInboxPage` / ticket reply composer.; `/` triggers list; arrow/enter to insert; variables resolved from ticket context; usage counter bumps.
- [ ] **Response Templates** ([[../../domains/support/canned-responses/features/response-templates|spec]]) — hand-check: open `CannedResponseResource` (`/support/canned-responses`).; create/edit; toggle shared (gated); duplicate shortcut rejected inline.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Support Analytics — `support.support-analytics`

Build: `/flowflex:start support.support-analytics` · Done: `/flowflex:done support.support-analytics` · Spec: [[../../domains/support/support-analytics/_module|hub]] · Hard deps: support.tickets, core.billing, core.rbac

- [ ] **CSAT Survey** ([[../../domains/support/support-analytics/features/csat-survey|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Support Dashboard** ([[../../domains/support/support-analytics/features/support-dashboard|spec]]) — hand-check: open "Support Dashboard" (`/support/dashboard`) — Filament dashboard page (ui-strategy row #6) + `leandrocfe/filame; change date range → widgets refresh; hover charts for detail; 60s poll.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Automations — `support.automations`

Build: `/flowflex:start support.automations` · Done: `/flowflex:done support.automations` · Spec: [[../../domains/support/automations/_module|hub]] · Hard deps: support.tickets, core.billing, core.rbac, foundation.queues

- [ ] **Automation Rules** ([[../../domains/support/automations/features/automation-rules|spec]]) — hand-check: open `AutomationRuleResource` (`/support/automations`).; drag to reorder; add condition/action rows (registry-driven selects); toggle active; test-run preview against a sample ticket.
- [ ] **Time-Based Rules** ([[../../domains/support/automations/features/time-based-rules|spec]]) — hand-check: background — trigger it (: `RunTimeBasedRulesCommand` (every 15 min).), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Live Chat — `support.live-chat`

Build: `/flowflex:start support.live-chat` · Done: `/flowflex:done support.live-chat` · Spec: [[../../domains/support/live-chat/_module|hub]] · Hard deps: support.tickets, core.billing, core.rbac, foundation.queues

- [ ] **Agent Queue** ([[../../domains/support/live-chat/features/agent-queue|spec]]) — hand-check: open "Chat Queue" (`/support/chat`) — Filament custom Page + Reverb, ui-strategy row #8; availability toggle via a ; claim a waiting chat; type reply (broadcast); see visitor typing; mark read; insert canned; convert to ticket.
- [ ] **Chat-to-Ticket** ([[../../domains/support/live-chat/features/chat-to-ticket|spec]]) — hand-check: open action within `ChatQueuePage` (`/support/chat`).; click convert → transcript packaged → ticket created via `TicketService` → link shown.
- [ ] **Chat Widget** ([[../../domains/support/live-chat/features/chat-widget|spec]]) — hand-check: open chat bubble injected on the customer site; served via `GET /chat/widget.js` (`ChatWidgetController`).; open → start chat; type → send (optimistic + broadcast); agent typing indicator; offline → "leave a message" (creates ticket).
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### SLA Management — `support.sla`

Build: `/flowflex:start support.sla` · Done: `/flowflex:done support.sla` · Spec: [[../../domains/support/sla/_module|hub]] · Hard deps: support.tickets, core.billing, core.rbac, core.notifications, core.settings

- [ ] **Breach Monitoring** ([[../../domains/support/sla/features/breach-monitoring|spec]]) — hand-check: open "SLA Monitor" (`/support/sla-monitor`) — Filament custom Page + Reverb, ui-strategy row #8-style; `SlaComplian; ticket crosses threshold → live row recolour + toast; click ticket → open in inbox.
- [ ] **SLA Policies** ([[../../domains/support/sla/features/sla-policies|spec]]) — hand-check: open `SlaPolicyResource` (`/support/sla-policies`).; add/remove target rows; validation resolution > first-response; save → `CreateSlaPolicyData`.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Tickets — `support.tickets`

Build: `/flowflex:start support.tickets` · Done: `/flowflex:done support.tickets` · Spec: [[../../domains/support/tickets/_module|hub]] · Hard deps: core.billing, core.rbac, core.files, core.notifications, foundation.email

- [ ] **Email-to-Ticket** ([[../../domains/support/tickets/features/email-to-ticket|spec]]) — hand-check: background — trigger it (: `POST /webhooks/support/inbound-email` (signed).), then check the visible result named in the spec
- [ ] **Ticket Inbox** ([[../../domains/support/tickets/features/ticket-inbox|spec]]) — hand-check: open "Ticket Inbox" (`/support/inbox`) — Filament custom Page (Livewire) + Reverb, ui-strategy row #8.; click ticket → load conversation; type reply → send (optimistic append + broadcast); new ticket arrives → toast + list prepend; as
- [ ] **Ticket Lifecycle** ([[../../domains/support/tickets/features/ticket-lifecycle|spec]]) — hand-check: open `TicketResource` (`/support/tickets`) + view page.; status transition action (guarded, confirm) → `TicketService` transition; reply composer (public vs internal-note toggle) → `Ticke
- [ ] **Ticket Merge** ([[../../domains/support/tickets/features/ticket-merge|spec]]) — hand-check: open action on `TicketResource` / ticket view (`/support/tickets`).; pick keep target → confirm → replies reassigned, source closed with link banner.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean
