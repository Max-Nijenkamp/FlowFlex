---
type: gap
severity: low
category: spec
status: open
domain: crm
color: "#F97316"
discovered: 2026-07-03
discovered-in: crm.contracts
---

# Gap — contracts renewals queue page missing from Build Manifest

## Context

Wave 2 batch 2 propagation: crm/contracts renewal-tracking feature declares a renewals queue page (`/crm/renewals`) and the `crm.contracts.renew` permission.

## Problem

`_module.md` Build Manifest lists no `ContractRenewalsPage.php`; only the renewals widget exists in the manifest.

## Impact

Build worker following the manifest skips the queue page while the feature note + permission imply it; artifact registry scrape will disagree with the manifest.

## Proposed Solution

Decide: add `app/Filament/Crm/Pages/ContractRenewalsPage.php` (+ view) to the Build Manifest, or downgrade the feature to widget-only and update the feature note. Same family as [[gap-bank-accounts-import-page-not-in-manifest]].
