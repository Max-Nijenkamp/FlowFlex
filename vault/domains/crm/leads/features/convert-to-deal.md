---
domain: crm
module: leads
type: feature
feature: convert-to-deal
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Convert to Deal

A qualified lead is converted into a pipeline deal, creating or matching a contact along the way. Planned behaviour, tense-softened from a retro-documented build.

## Trigger

- "Convert to deal" row action on `LeadResource`, gated `crm.leads.convert`, hidden once the lead is `converted`.

## Flow

1. **Guard idempotency** — if the lead is already `converted` (or `converted_deal_id` set), refuse with `ValidationException`.
2. **Resolve pipeline** — find the default pipeline + its first stage; throw `ValidationException` if none exists.
3. **Match/create contact** — look up a `crm_contacts` row by the lead email within the company; create it if none matches.
4. **Create deal** — insert a `crm_deals` row in the first stage, seeded with `estimated_value_cents` and the stage probability.
5. **Stamp lead** — `status = converted`, `converted_deal_id`, `converted_at = now()`.
6. All steps run inside a single DB transaction.

## Edge Cases (undocumented — see [[../unknowns]])

- Blank lead email → contact creation behaviour unspecified.
- No default pipeline configured → conversion blocked with a validation error.
- Re-running convert on a converted lead → refused.

## Test Checklist

- [ ] Convert creates a deal in default pipeline first stage with lead value + stage probability.
- [ ] Convert creates/links a contact from the lead email.
- [ ] Already-converted lead refuses reconversion.

## UI

- **Kind**: simple-resource with a "Convert to deal" row action modal on `LeadResource`.
- **Page**: `LeadResource` list/view at `/crm/leads`; convert is a row action, hidden once `status = converted`.
- **Layout**: standard leads resource; convert opens a modal confirming target pipeline + first stage and the contact match/create outcome.
- **Key interactions**: row action → confirm modal → single-transaction convert (`ConvertLeadAction`) → deep-link to the new deal.
- **States**: empty (no leads) · loading (converting) · error (no default pipeline, or already converted → `ValidationException`) · selected (lead row highlighted). *(assumed — no designed UX spec)*
- **Gating**: `crm.leads.convert`.

## Data

- Owns / writes: `crm_leads` only (`status`, `converted_deal_id`, `converted_at`).
- Reads / Commands: `crm.contacts` via `ContactService::findOrCreateByEmail`, `crm.deals` via `DealService`, `crm.pipeline` for the default pipeline's first stage.
- Cross-domain writes: NONE directly — the deal and contact rows are created through those modules' service APIs, never by writing `crm_deals` / `crm_contacts` from leads ([[../../../../security/data-ownership]]).

> [!warning] UNVERIFIED
> Whether convert should emit a `LeadConverted` cross-domain event is undecided (see [[../unknowns]]). Documented here as command-API calls, not a fired event.

## Relations

- Consumes: nothing.
- Feeds: nothing significant confirmed — `LeadConverted` event is *(assumed)* / unevaluated ([[../unknowns]]).
- Shared entity: `crm_contacts` (via `ContactService`), `crm_deals` (via `DealService`), `crm_pipeline_stages` (default pipeline first stage) — all owned elsewhere and reached through their services.

## Related

- [[../architecture]] · [[../data-model]] · [[../../deals/_module|Deals]] · [[../../contacts/_module|Contacts]] · [[../../pipeline/_module|Pipeline]]
