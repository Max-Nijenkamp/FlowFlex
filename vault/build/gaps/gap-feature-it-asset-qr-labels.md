---
type: gap
severity: medium
category: feature
status: open
domain: it
color: "#F97316"
discovered: 2026-07-03
discovered-in: it.assets
---

# Gap: No QR / barcode asset-label printing

## Context

Wave 3a feature-gap research (2026-07). Snipe-IT — the tool FlowFlex IT displaces for assets — treats
**QR + 1D barcode asset-label generation** as table-stakes: labels carry a scannable code that deep-links to
the asset record. Bulk asset *data* import is already covered (soft dep on `core.import`), but label output
is not.

## Problem

The asset record ([[../../domains/it/asset-inventory/_module]]) stores an `asset_tag` field, but there is
**no feature or artifact to generate/print QR or barcode labels** for tagging physical devices. Without it,
the "scan the sticker on the laptop → open its record / raise a ticket" loop (a headline unified
asset+helpdesk angle in [[../../domains/it/_opportunities]]) cannot close.

## Impact

- Cannot physically tag assets from within FlowFlex → forces a separate label tool, undercutting the
  single-pane pitch.
- Blocks the scan-to-ticket helpdesk flow proposed in the 2026-07 opportunity refresh.

## Proposed Solution

Add a "print asset labels" action rendering a printable label sheet (QR encoding the asset deep-link +
`asset_tag` barcode). Implementable with **already-chosen packages, no new dependency**:
`simplesoftwareio/simple-qrcode` + `spatie/laravel-pdf`. Target:
[[../../domains/it/asset-inventory/_module|it.assets]] (feeds the [[../../domains/it/helpdesk/_module|helpdesk]]
scan-to-ticket flow). See [[../../domains/it/_opportunities]] (2026-07 refresh).
