---
domain: hr
module: recruitment
feature: offers
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Offers

Not built — see [[../_module]].

## Purpose

Generate, send, and track offer letters with salary and start-date fields.

## Intended behavior

- Create via `makeOffer(CreateOfferData)` — `salary_cents`, `currency`, `start_date` (after:today).
- Send via `sendOffer($offerId)` — queues `OfferMail`, sets `sent_at`.
- Status flow: `draft` → `sent` → `accepted` / `declined`; `accepted_at` recorded on acceptance.
- Salary stored encrypted (`hr_offers.salary_raw`, minor-unit integer, brick/money for arithmetic) — see [[../security]] + [[../../../../security/encryption]].

## Tables / permissions

- Table: `hr_offers`.
- Permissions: `hr.recruitment.manage-offers`, `hr.recruitment.view-any`.
- Filament: `OfferResource` (create, send, track).

## UI

- **Kind**: simple-resource (`OfferResource`, state machine on status)
- **Page**: "Offers" (`/hr/offers`)
- **Layout**: table — applicant, salary (masked/authorized), currency, start date, status badge (draft/sent/accepted/declined), sent-at; create form with salary, currency, start-date (after:today); **Send** action queues `OfferMail`.
- **Key interactions**: `makeOffer` (create draft); `sendOffer` (draft → sent, sets `sent_at`); mark accepted/declined (records `accepted_at`).
- **States**: empty ("No offers yet") · loading (table skeleton) · error (toast on invalid transition / send failure) · selected (row opens offer detail; salary shown only to authorized).
- **Gating**: view requires `hr.recruitment.view-any`; create/send/manage requires `hr.recruitment.manage-offers`. Salary is encrypted at rest (`salary_raw`) and access-gated.

## Data

- Owns / writes: `hr_offers` (encrypted `salary_raw`, minor-unit integer via brick/money)
- Reads: reads `hr_applicants` within this module
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none *(acceptance advances the applicant toward hire; conversion handled in [[applicant-to-employee-conversion]])*
- Shared entity: none

## Test Checklist

### Unit
- [ ] `start_date` must be `after:today`; `salary_cents` handled via `brick/money`
- [ ] Offer status transitions valid only along `draft → sent → accepted` / `declined`

### Feature (Pest)
- [ ] `sendOffer` moves `draft → sent`, sets `sent_at`, queues `OfferMail` (`panel-action` comms limiter)
- [ ] `salary_raw` encrypted at rest; acceptance records `accepted_at`; company A cannot see company B offers

### Livewire
- [ ] Create/send/manage denied without `hr.recruitment.manage-offers`
- [ ] Salary masked unless the viewer is authorized

## Related

- [[../_module]] · [[../data-model]] · [[../security]]
