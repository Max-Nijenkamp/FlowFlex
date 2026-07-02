---
domain: legal
module: compliance-registers
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Compliance Registers — Security

## Access contract

`canAccess() = Auth::user()->can('legal.compliance.view-any') && BillingService::hasModule('legal.compliance')` per [[../../../architecture/filament-patterns]] #1.

## Evidence upload hardening (medium — per [[../../../build/security-audit-2026-06-11]])

- Control-evidence Media Library collection: allowed types (pdf/png/jpg/docx), max size, `companies/{id}/`-scoped storage path.

## Permissions

`legal.compliance.view-any` · `legal.compliance.manage-frameworks` · `legal.compliance.update-controls` · `legal.compliance.manage-tasks`

## Data ownership

Writes only `legal_frameworks`, `legal_controls`, `legal_compliance_tasks`; policy + privacy data read-only; reminders via `core.notifications` ([[../../../security/data-ownership]]).
