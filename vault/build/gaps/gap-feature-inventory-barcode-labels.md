---
type: gap
severity: medium
category: feature
status: accepted
domain: operations
color: "#F97316"
discovered: 2026-07-03
discovered-in: operations.inventory
---

# Gap: No barcode / QR label printing for items or bin locations

## Context

Wave 3a feature-gap research (2026-07). Barcode inventory tools treat **built-in label printing** as
table-stakes for SMEs — a built-in generator plus printable label sheets (SKU, serial, bin location) is one
of the most-cited "must-have" capabilities for teams migrating off spreadsheets.

## Problem

The inventory spec flags barcode *scanning* as a gap ([[../../domains/operations/inventory/unknowns]] — "SKU
doubles as barcode for v1") but there is **no feature, build-manifest artifact, or package for generating and
printing barcode/QR labels** for items or for warehouse bins/locations. Scanning without a way to produce the
labels is a half-loop.

## Impact

- Cannot label new stock or bin locations without a third-party label tool → blocks the scan workflow the
  opportunity radar leans on.
- Warehouses module owns bin/location identity but has no way to emit location labels.

## Proposed Solution

Add a "print labels" action (item labels + bin/location labels) that renders a printable label sheet.
Implementable with **already-chosen packages, no new dependency**: `simplesoftwareio/simple-qrcode` (QR /
Code128) + `spatie/laravel-pdf` (label-sheet PDF). Target modules:
[[../../domains/operations/inventory/_module|operations.inventory]] and
[[../../domains/operations/warehouses/_module|operations.warehouses]]. Consider a dedicated `barcode`/`sku`
column rather than overloading SKU. See [[../../domains/operations/_opportunities]] (2026-07 refresh).
