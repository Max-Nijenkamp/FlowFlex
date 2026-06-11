---
type: gap
severity: high
category: architecture
status: open
domain: All
color: "#F97316"
discovered: 2026-06-11
discovered-in: vault-wide
---

# UI kinds not in the ui-strategy decision table (need ADR)

## Context
Surfaced by the 2026-06-11 vault security & UI spec-conformance audit ([[build/security-audit-2026-06-11]]). Systemic pattern, not a one-off — tracked as a single gap with the per-spec worklist held in the audit report.

## Problem
Heat-maps, gallery directories and spatial floor-maps are mapped to wrong/non-existent ui-strategy rows. Per the locked all-Filament hybrid ADR, a new UI kind requires an ADR + a new table row before build.

## Impact
HIGH in lms (skills-matrix heat-map, mentoring directory) and workplace (desk-booking floor map).

## Proposed Solution
Raise 3 ADRs adding the rows (or remapping to existing rows) before those modules build: skills-matrix heat-map, mentor directory gallery, desk-booking floor map. See [[architecture/ui-strategy]] and [[build/security-audit-2026-06-11]] (UI-ROW).

## Related
- [[build/security-audit-2026-06-11]]
- [[build/decisions/decision-2026-06-11-security-contract-hardening]]
