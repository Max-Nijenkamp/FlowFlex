---
domain: marketing
module: forms
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Forms — Security

Parent: [[_module]]

The public submit endpoint is the primary attack surface — unauthenticated, cross-origin, writing PII.

## Public submit guard (HIGH)

- `POST /f/{slug}` runs **outside the Sanctum session guard** as an explicit public route (no auth).
- Resolves **company by form slug** — never by session.
- **CSRF-exempt** (cross-site embeds) with documented **allowed-origin** handling; only active forms accept submissions (inactive → 404).

## Spam & abuse

- Honeypot field: filled → silently dropped (200, no store, no event).
- Per-IP rate limit on submit (throttle middleware).
- Optional captcha (Turnstile *(assumed)*) when configured.

## Permissions (authenticated side)

`marketing.forms.view-any` · `marketing.forms.create` · `marketing.forms.update` · `marketing.forms.view-submissions`. Resources gate on `canAccess()`. Submission export gated on `view-submissions`.

## PII & GDPR

- Consent-checkbox field type captures explicit marketing consent at capture time.
- Submissions purge with contact erasure ([[../../../architecture/data-lifecycle]]).

## Data ownership

Writes only `mkt_forms`, `mkt_form_submissions`. Contact creation happens in CRM's own listener on `FormSubmissionReceived` — forms never writes CRM tables ([[../../../security/data-ownership]]).

## Related

- [[_module]] · [[api]] · [[../../../architecture/security]] · [[../../../security/authn-authz]]
