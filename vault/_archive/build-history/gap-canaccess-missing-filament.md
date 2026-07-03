---
type: gap
severity: high
category: architecture
status: resolved
resolved: 2026-06-11
domain: All
color: "#F97316"
discovered: 2026-06-11
discovered-in: vault-wide
---

# canAccess() missing on Filament artifacts (~100+ HIGH, all 31 domains)

## Context
Surfaced by the 2026-06-11 vault security & UI spec-conformance audit ([[build/security-audit-2026-06-11]]). Systemic pattern, not a one-off — tracked as a single gap with the per-spec worklist held in the audit report.

## Problem
Specs declare resources/pages/widgets without the mandatory `canAccess() = permission + BillingService::hasModule()` contract. Filament auto-registers custom-page routes but does NOT auto-gate them, so a missing canAccess() exposes the URL to any authenticated user regardless of role or module subscription.

## Impact
~100+ Filament artifacts across all 31 domains lack a stated access contract. Custom pages (Kanban, dashboards, builders, kiosks, wizards) are highest risk. #1 systemic finding.

## Proposed Solution
Resolved at the template level by [[build/decisions/decision-2026-06-11-security-contract-hardening]]: spec-template `## Filament` skeleton now mandates the contract; filament-patterns #1 strengthened. Backfill per-module at `/flowflex:start` using the per-spec list in [[build/security-audit-2026-06-11]]. Prioritise hr.payroll, hr.compensation, core.rbac, core.audit-log, core.billing, it.mdm.

## Resolution

Access-contract line backfilled into 165 specs (every spec with a `## Filament` section), derived from each spec's `permission-prefix` + `module-key`. Template + filament-patterns #1 now mandate it for new specs. Code-level enforcement happens at build per DoD.

## Related
- [[build/security-audit-2026-06-11]]
- [[build/decisions/decision-2026-06-11-security-contract-hardening]]
