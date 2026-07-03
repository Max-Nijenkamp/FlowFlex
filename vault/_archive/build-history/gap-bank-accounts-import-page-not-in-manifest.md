---
type: gap
severity: low
category: spec
status: open
domain: finance
color: "#F97316"
discovered: 2026-07-03
discovered-in: finance.bank-accounts
---

# Gap — ImportStatementPage missing from bank-accounts Build Manifest

## Context

`finance/bank-accounts/architecture.md` lists `ImportStatementPage` (#7 wizard) in `## Filament Artifacts`, discovered during wave 2 batch 1 v3 propagation verification.

## Problem

The page is absent from `_module.md`'s `## Build Manifest`, which lists only the two resources. Artifact table and manifest disagree.

## Impact

Build worker following the manifest verbatim would skip the statement-import wizard; artifact registry (wave 3b) will scrape a page with no manifest file path.

## Proposed Solution

Confirm the page is in scope, then add `app/Filament/Finance/Pages/ImportStatementPage.php` (+ view) to the Build Manifest and a matching rollup test line. Not fixed during propagation to avoid inventing a manifest entry.
