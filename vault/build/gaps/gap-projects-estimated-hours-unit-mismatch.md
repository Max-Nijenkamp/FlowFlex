---
type: gap
severity: medium
category: data-model
status: open
domain: projects
color: "#F97316"
discovered: 2026-07-03
discovered-in: projects.tasks
---

# Gap — estimated_hours decimal conflicts with minutes-int time convention

## Context

Wave 2 batch 2 v3 propagation over the projects domain.

## Problem

`proj_tasks.estimated_hours` and `proj_template_tasks.estimated_hours` are decimal HOURS, while the platform time convention (followed by time-tracking's `minutes_logged`) is integer minutes.

## Impact

Mixed units across the same domain: workload/capacity math (estimates vs logged time) needs ad-hoc conversion and invites float drift, contradicting the integers-only arithmetic rule used for money and time elsewhere.

## Proposed Solution

Reconciliation decision: either migrate estimates to `estimated_minutes` (int) in tasks + templates + dependent workload calculations, or record an ADR explicitly exempting coarse hour estimates. Prefer migration for consistency.
