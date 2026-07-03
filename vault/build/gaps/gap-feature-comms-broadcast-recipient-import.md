---
type: gap
severity: medium
category: feature
status: accepted
domain: communications
color: "#F97316"
discovered: 2026-07-03
discovered-in: communications.broadcast
---

# Gap: No CSV recipient import for broadcasts (external lists)

## Context

Wave 3a feature-gap research (2026-07). Parallels the already-logged
[[gap-feature-marketing-subscriber-import]] and [[gap-feature-events-attendee-import]] holes — the same
"bring a list we already have" on-ramp is missing in comms broadcast.

## Problem

[[../../domains/communications/broadcast/features/recipient-materialisation]] resolves audiences from exactly
three sources: `crm.segments`, `hr.profiles` employee groups, and a "manual → provided list". There is **no
bulk CSV/XLSX import** of external recipients (addresses/phone numbers that are not CRM contacts or
employees). SMEs running a one-off blast to an imported list (event guests, a purchased/collected list, a
migrated Mailchimp export) have no path in.

## Impact

- Blocks the migrate-off-Mailchimp / one-off-blast use case; broadcast can only reach people already modelled
  in CRM/HR.
- Inconsistent with the platform's other list-import on-ramps (marketing, events).

## Proposed Solution

Add a CSV/XLSX recipient importer feeding `comms_broadcast_recipients` (address + name snapshot), running the
same dedupe + opt-out + deliverability exclusions as `recipient-materialisation`. Implementable with the
already-chosen `maatwebsite/laravel-excel` — **no new dependency**. Must honour `OptOutService` and E.164
normalisation (`propaganistas/laravel-phone`) on import. Target:
[[../../domains/communications/broadcast/_module|communications.broadcast]]. See
[[../../domains/communications/_opportunities]] (2026-07 refresh).
