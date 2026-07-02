---
domain: crm
module: leads
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Leads — Decisions

> **No prior vault spec existed for Leads.** The flat source file was a retro-doc of a founder-requested build (dated 2026-06-14), not a designed spec. Every decision below is **reconstructed / assumed**, not deliberately designed. All should be re-ratified during the v2 spec rebuild. See [[unknowns]].

## ADR: Convert is idempotent (reconstructed)

- **Context:** A qualified lead can be converted into a pipeline deal.
- **Decision:** `ConvertLeadAction` refuses to reconvert an already-converted lead (guarded on `status` / `converted_deal_id`), throwing `ValidationException`. The whole conversion runs in a DB transaction.
- **Consequences:** No duplicate deals from double-clicks; the lead ↔ deal link is one-to-one.

## ADR: Contact matched by email on convert (reconstructed)

- **Context:** A converted lead should tie to a CRM contact.
- **Decision:** On convert, match an existing `crm_contacts` row by email within the company; create one if none matches.
- **Consequences:** Soft dependency on [[../contacts/_module|Contacts]]; behaviour when the lead email is blank is undocumented (see [[unknowns]]).

## ADR: Target the default pipeline's first stage (reconstructed)

- **Context:** A converted lead needs a pipeline + stage.
- **Decision:** Convert targets the **default** pipeline's first stage; throws `ValidationException` if no pipeline/stage exists.
- **Consequences:** Soft dependency on [[../pipeline/_module|Pipeline]] and [[../deals/_module|Deals]]; conversion is blocked until a default pipeline is configured.

## ADR: Nav sort `-1` (reconstructed)

- **Decision:** `LeadResource` sits in the "Contacts" nav group with sort `-1` so it appears above Contacts.
- **Consequences:** Purely presentational; reflects the capture-before-contact funnel order.
