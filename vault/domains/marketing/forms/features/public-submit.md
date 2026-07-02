---
domain: marketing
module: forms
feature: public-submit
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
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

## Unknowns

> [!warning] UNVERIFIED
> Write-back of the CRM-created `contact_id` onto the submission without a cross-domain write. See [[../unknowns]].

## Related

- [[../_module|Forms]] · [[../api]] · [[../security]]
