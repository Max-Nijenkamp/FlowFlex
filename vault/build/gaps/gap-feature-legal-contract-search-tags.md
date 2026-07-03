---
type: gap
severity: medium
category: feature
status: accepted
domain: legal
color: "#F97316"
discovered: 2026-07-03
discovered-in: legal.contracts
---

# Contract full-text search + tagging missing

## Context
`legal.contracts` models the contract record, lifecycle queue, and renewal widget, but its `_module.md`
declares **no full-text search and no tagging**. The only "search" surface is standard Filament table
filters. Contract organization by type/party/status and clause-level lookup are unspecced.

## Problem
"Tag, search, and pull up documents in seconds" is the headline capability SME buyers cite for
ContractWorks and the class of tools FlowFlex Legal displaces. Without full-text search across contract
bodies and metadata, and without tag-based organization, the repository is only as findable as its column
filters — a known adoption killer for CLM (radar #1: adoption over feature-bloat).

## Impact
- Weakens the "unified legal operating system" positioning (radar #5) — the repository is not searchable.
- Users cannot find a clause/party fast; tagging (the incumbent's smart-tagging parallel) is absent.

## Proposed Solution
Both are covered by the **already-chosen** package list — no new dependencies:
- Full-text search via `laravel/scout` on the Meilisearch driver (index title, counterparty, type,
  extracted body text), respecting `CompanyScope` and the confidential-matter access layer.
- Tag-based organization via `spatie/laravel-tags` (already used across CRM/Support/Projects/Comms/DMS).

Spec the searchable attributes + tenant/confidentiality scoping in the contract-repository feature note.

## Related
- [[../../domains/legal/legal-contracts/_module]] · [[../../domains/legal/_opportunities]]
- [[../../architecture/search]] · [[../../security/data-ownership]]
