---
type: gap
severity: low
category: architecture
status: open
domain: finance
color: "#F97316"
discovered: 2026-07-03
discovered-in: finance.bank-accounts
---

# Gap — no ui-strategy row for two-panel matcher pages

## Context

Wave 2 batch 1 propagation: bank reconciliation (unreconciled transactions vs suggested journal lines) and the AP `PaymentRunPage` (batch select → execute) are both two-panel matcher interactions.

## Problem

No ui-strategy decision-table row or page-blueprints kind fits; both specs cite closest row #9 (Report Builder) with a `#9*` footnote flag per the no-invented-rows rule.

## Impact

Two specs cite an approximate row; future matcher-style screens (e.g. dedupe review, payment allocation) have no canonical blueprint.

## Proposed Solution

Draft ADR proposing a "two-panel matcher" ui-strategy row + page-blueprints kind, then re-cite both specs. Candidate to bundle with the wave 3a POS/kiosk row question.
