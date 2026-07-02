---
domain: marketing
module: forms
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Forms — Unknowns

Parent: [[_module]]

## Assumed Items

- Captcha = Cloudflare Turnstile *(assumed)*; config surface not specced.
- `contact_id` mirrored back onto the submission read-only after CRM creates the contact *(assumed)* — the mechanism (event / projection) is unconfirmed.
- Conditional fields deferred *(assumed)*.

## Open Questions

> [!warning] UNVERIFIED
> How the CRM-created `contact_id` is written back onto `mkt_form_submissions` without violating data-ownership. Likely a `ContactCreatedFromForm` event carrying `submission_id` that forms' own listener consumes to set its own row. Needs an ADR.

- Origin allow-list management: per-form domains vs. company-wide.
- File-upload field type — allowed? scanning?
- Multi-step forms.

## Related

- [[_module]] · [[decisions]] · [[../_opportunities]]
