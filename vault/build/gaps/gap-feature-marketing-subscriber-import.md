---
type: gap
severity: medium
category: feature
status: accepted
domain: marketing
color: "#F97316"
discovered: 2026-07-03
discovered-in: marketing.campaigns
---

# Gap: Marketing has no subscriber-list import (no `core.data-import` importer)

## Context

`core.data-import`'s [[../../domains/core/data-import/features/importer-registry|ImporterRegistry]] is the
platform-wide bulk-import mechanism: each domain registers an importer (`hr.employees`, `crm.contacts`,
expense items, products). Marketing does **not** register one. Campaign audiences today materialise only
from `crm.segments` (read via `SegmentService`), so a raw imported subscriber list has nowhere to land.

## Problem

Teams migrating off Mailchimp / ActiveCampaign / Brevo export their audience as a ZIP of CSVs and expect
to import it into the new tool. FlowFlex Marketing can read CRM contacts, but there is no path to bring in
a standalone marketing subscriber list (with consent status, tags, unsubscribe flags) that isn't already a
CRM contact — the single most common switching on-ramp for an email tool.

## Impact

Blocks the "switch from Mailchimp" story for [[../../domains/marketing/campaigns/_module|marketing.campaigns]]
and [[../../domains/marketing/_opportunities|opportunity #1/#3/#4]] (transparent pricing / restored automation
positioning is moot if you can't get your list in). Package-fit — no new dependency needed.

## Proposed Solution

Register a `marketing.audience` importer with `core.data-import` using `maatwebsite/laravel-excel`, mapping
CSV columns to a Create DTO that seeds contacts (find-or-create into `crm.contacts` per data-ownership) plus
marketing consent/suppression state on the `mkt_*` side. Honour the shared `mkt_unsubscribes` suppression
list and per-channel consent ([[../../domains/marketing/_opportunities|opportunity #8]]) on import.

## Sources

- [Export your Mailchimp list — ZIP of CSVs, migrating to a new tool (Mailsoftly, 2026)](https://mailsoftly.com/blog/how-to-export-mailchimp-list/)
- [Import contacts to a platform via CSV (Mailchimp Help)](https://mailchimp.com/help/import-subscribers-to-a-list/)
