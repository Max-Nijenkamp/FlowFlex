---
domain: core
module: data-privacy
feature: dsar-queue
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: DSAR Queue

Parent: [[../_module]] · See [[../architecture]]

Logs and processes data-subject access + erasure requests.

- `DsarRequestResource` (`/app`, Settings nav) lists requests with a deadline-countdown column and process/reject actions; access requests expose a result download.
- Creating a request fires `DSARRequestSubmitted` and sets `due_at = created + 30 days`.
- `DsarDeadlineReminderCommand` (notifications queue, daily) reminds at `due_at-7d` and `due_at-1d`, once each.
- Processing moves `received → in-progress`; success → `completed`, identity-unverified or legal-hold → `rejected`.

## UI

- **Kind**: simple-resource
- **Page**: `DsarRequestResource` — list/create/view under `/app` (Settings nav group)
- **Layout**: table of requests with columns for subject email, request type (access/erasure), status badge, and a deadline-countdown column (days to `due_at`); row actions Process / Reject; access-request rows expose a result-ZIP download. Create form is the `CreateDsarRequestData` fields (subject email + type).
- **Key interactions**: staff logs a request → the row appears `received` with a 30-day countdown → staff clicks Process (moves to `in-progress`, dispatches the access/erasure job) or Reject with a reason → on success the row shows `completed` and, for access, a download link.
- **States**: empty = no requests (empty-state prompt) · loading = job running, status `in-progress` · error = job/infra failure surfaced on the request · selected = a request row open with process/reject actions and (if access) the download.
- **Gating**: `core.privacy.view-any` to list/view, `core.privacy.create` to log, `core.privacy.process` for Process/Reject (+ `BillingService::hasModule('core.privacy')`).

## Data

- Owns / writes: `dsar_requests` (this module's table) — create, status transitions, `completed_at`, `result_path`. Compliance rows are never hard-deleted with tenant data.
- Reads: Company Settings for DSAR contact email + retention config (read-only, [[../company-settings/_module]]).
- Cross-domain writes: none directly — actual data access/erasure runs in the export/erasure jobs; this feature only writes the DSAR row and fires an event ([[../../../../security/data-ownership]]).

## Relations

- Feeds: `DSARRequestSubmitted` (fired on create) → consumed by Notifications (deadline/ack) now, Legal in P3.
- Consumes: none.
- Shared entity: DSAR contact email + retention window are reference config owned by [[../company-settings/_module]] (read-only).
