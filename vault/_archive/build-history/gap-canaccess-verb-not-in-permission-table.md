---
type: gap
severity: medium
category: spec
status: open
domain: All
color: "#F97316"
discovered: 2026-07-03
discovered-in: communications.email-channel
---

# Gap — canAccess() cites permission verbs absent from security.md tables

## Context

Wave 2 batch 2 propagation: 5 of 8 communications modules (email-channel, whatsapp, sms-channel, internal-messaging, comms-analytics) had `canAccess()` gating on a `.view-any` verb that their own `## Permissions` table never defined. Reconciled per module (verb added *(assumed)* or gate re-pointed at a defined verb). live-chat (support) had the same defect. crm adds three more unreconciled cases (flagged, not fixed): forecasting gate cites `crm.forecasting.view-any` (table has only view-own/view-team), pipeline cites `crm.pipeline.view-any` (table has `crm.pipeline.view`), price-management volume-discounts feature cites `crm.pricing.update`/`.view` (table uses manage-products/manage-price-books/view-any).

## Problem

No structural check that the verb named in the access contract exists in the module's permission set. Pattern likely recurs in other domains.

## Impact

Built module would gate on a never-seeded permission → panel invisible for everyone, or worse, developer seeds an ad-hoc verb outside the seeder registry.

## Proposed Solution

1. One-shot lint: grep every architecture.md access-contract verb and assert it appears in the sibling security.md permission table (candidate wave 3b check alongside the artifact registry scrape).
2. Add the rule to the spec-template v3 checklist / way-of-working quality gates via a small consistency ADR.
