---
domain: marketing
module: forms
feature: public-submit
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Public Submit

Validate, store, and fan out a submission — the event that seeds CRM, sequences and UTM.

## Behaviour

- `FormService::submit(slug, values, ip)`: validate against the definition; honeypot filled → silent 200 drop.
- Store `mkt_form_submissions`; fire `FormSubmissionReceived` (company_id, form_id, submission_id, email, fields).
- Soft effects: enrol in configured sequence; notify users; return thank-you / redirect.
- Per-IP rate limited; CSRF-exempt cross-origin.

## UI

- **Kind**: background (the POST handler behind [[embed-hosted]]'s form)
- **Page**: no dedicated page — `POST /f/{slug}` public route; submissions viewed in-app via `FormSubmissionResource` (read-only, export).
- **States**: n/a for the endpoint; the hosted page shows success/error. Submission list: empty · loading · error · selected (row → detail).
- **Gating**: public submit (no auth). Viewing submissions requires `marketing.forms.view-submissions`.

## Data

- Owns / writes: `mkt_form_submissions` (own module).
- Reads: form definition (own).
- Cross-domain writes: none — fans out via `FormSubmissionReceived`; CRM/sequences/UTM write their own tables ([[../../../../security/data-ownership]]).

## Relations

- Feeds: `FormSubmissionReceived` → [[../../../crm/contacts/_module|crm.contacts]] (find-or-create), [[../../email-sequences/_module|marketing.sequences]] (enrol), [[../../utm-tracking/_module|marketing.utm]] (touch).
- Shared entity: none written.

## Test Checklist

### Unit
- [ ] Honeypot filled -> silent 200 drop, nothing stored
- [ ] Values validated against the definition (required, email format)

### Feature (Pest)
- [ ] Valid submit stores `mkt_form_submissions` + fires `FormSubmissionReceived` with company_id as scalar
- [ ] Per-IP rate limit: over-limit returns 429; CSRF-exempt cross-origin path works
- [ ] Tenant isolation: submission lands on the owning company's form only

### Livewire
- (none -- public endpoint, no panel UI)

## Unknowns

> [!warning] UNVERIFIED
> Write-back of the CRM-created `contact_id` onto the submission without a cross-domain write. See [[../unknowns]].

## Related

- [[../_module|Forms]] · [[../api]] · [[../security]]
