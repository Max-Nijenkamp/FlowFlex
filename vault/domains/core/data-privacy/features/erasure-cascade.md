---
domain: core
module: data-privacy
feature: erasure-cascade
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Erasure Cascade

Parent: [[../_module]] · See [[../architecture]] · [[../../../architecture/data-lifecycle]]

Soft-delete → anonymise → schedule hard delete, per table family.

- `ProcessErasureRequestJob` (default queue, `WithCompanyContext`) applies per-family cascade rules from [[../../../architecture/data-lifecycle]].
- Rules per family: e.g. `hr_employees` anonymised, FlowFlex-issued invoices untouched (legal hold), emergency contacts hard-deleted.
- Chunked and idempotent — anonymise writes are naturally re-runnable; per-family ordering keeps FK integrity.
- An erasure blocked by an open legal hold takes the `rejected` path with reason recorded.

## UI

- **Kind**: background
- **Page**: background (no page) — triggered by the Process action on an erasure-type request in [[dsar-queue]] (`DsarRequestResource`); the outcome (completed/rejected) shows on that DSAR row.
- **Layout**: n/a. Trigger: `DsarRequestResource` Process action on a `request_type = erasure` request dispatches `ProcessErasureRequestJob` (default queue, `WithCompanyContext`).
- **Key interactions**: none directly — staff drives it via the DSAR queue; the job runs the per-family cascade and flips the request to `completed` or `rejected`.
- **States**: empty = no erasure pending · loading = job running (`in-progress`) · error = infra failure surfaced on the request · selected = request row shows `completed` or `rejected` (reason).
- **Gating**: `core.privacy.process` (the triggering action) (+ `BillingService::hasModule('core.privacy')`).

## Data

- Owns / writes: `dsar_requests` (this module's table) — status transitions + `completed_at`.
- Reads: `PersonalDataRegistry::tablesFor($email)` to resolve which registered tables belong to the subject, and legal-hold state (read-only) to decide the reject path. Reads the per-family cascade rules from [[../../../architecture/data-lifecycle]].
- Cross-domain writes: **via events only.** Per [[../../../../security/data-ownership]], data-privacy must not write another domain's tables directly. The erasure orchestrator writes only `dsar_requests`; each source domain anonymises/deletes **its own** PII tables (e.g. HR erases `hr_employees`) by reacting to an erasure event / running its own registered eraser callback. Financial/legal-hold tables are skipped by their owning domain.

> [!warning] UNVERIFIED — the sibling notes ([[../architecture]], `_module.md` test checklist) describe `ProcessErasureRequestJob` itself anonymising `hr_employees` and hard-deleting emergency contacts directly. That would cross domain write-ownership. The constitution-compliant reading is event/callback-driven erasure where each domain erases its own tables; whether the built job dispatches per-domain erasers or writes cross-domain tables directly needs confirmation against the code (which is removed). Cascade family rules live in [[../../../architecture/data-lifecycle]].

## Relations

- Consumes: PII-table + eraser registrations pushed into `PersonalDataRegistry` by each domain's ServiceProvider (read-only registry).
- Feeds: an erasure trigger per subject/table-family → consumed by each owning domain's eraser (which writes its own tables). *(mechanism assumed — see UNVERIFIED above)*
- Shared entity: PII table/field + legal-hold definitions are owned by the source domains and by [[../../../architecture/data-lifecycle]]; this feature reads them and writes only its own `dsar_requests`.
