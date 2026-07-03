---
domain: marketing
module: forms
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
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

## Rate limiting

| Action | Category | Limiter |
|---|---|---|
| Public submit (`POST /f/{slug}`) | public endpoint (writes PII) | `api` *(assumed — no dedicated public-endpoint limiter exists yet; per-IP throttle + honeypot + optional captcha is an open reconciliation item, see [[unknowns]])* ([[../../../architecture/security]]) |
| Submission export (panel action) | generates a file | `exports` ([[../../../architecture/security]]) |

## Permissions (authenticated side)

| Permission | Grants |
|---|---|
| `marketing.forms.view-any` | Form list |
| `marketing.forms.create` | Create a form |
| `marketing.forms.update` | Edit a form; toggle active |
| `marketing.forms.delete` | Soft-delete a form |
| `marketing.forms.view-submissions` | View + export submissions |

Resources gate on `canAccess()`. Submission export is gated on `view-submissions` and throttled via the `exports` limiter. The public submit path has no permission — it resolves company by slug. Seeded in `PermissionSeeder`.

**Verb-per-command check:** the active/inactive toggle maps to `.update`; export maps to `.view-submissions`. No spatie state machine (forms are a simple active flag). All covered.

## PII & GDPR

- Consent-checkbox field type captures explicit marketing consent at capture time.
- Submissions purge with contact erasure ([[../../../architecture/data-lifecycle]]).
- Submissions store PII (email, and optionally name/phone) in the `mkt_form_submissions` values. Phone field values should normalise to E.164 and encryption-at-rest for the values blob is **open** — see the flag in [[unknowns]] *(assumed: not yet decided)*.

## Data ownership

Writes only `mkt_forms`, `mkt_form_submissions`. Contact creation happens in CRM's own listener on `FormSubmissionReceived` — forms never writes CRM tables ([[../../../security/data-ownership]]).

## Related

- [[_module]] · [[api]] · [[../../../architecture/security]] · [[../../../security/authn-authz]]
