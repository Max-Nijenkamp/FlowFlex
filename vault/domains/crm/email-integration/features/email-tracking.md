---
domain: crm
module: email-integration
type: feature
feature: email-tracking
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Email Tracking

Planned open + click tracking on outbound emails, via a guest-guarded pixel and click-redirect.

## Flow

1. `SendTrackedEmailAction` injects an **open pixel** and **wraps outbound links** before dispatch (via `SendEmailJob`, notifications queue).
2. Recipient opens the email → `TrackOpenController` (guest route) validates the per-email token *(assumed)* and stamps `opened_at` **once**.
3. Recipient clicks a wrapped link → `TrackClickController` (guest route) validates the token, stamps `clicked_at` once, and redirects **only to validated stored URLs**.

## Security

- Endpoints run on a guest route group isolated from authenticated guards — see [[../../../../security/authn-authz]].
- Named rate limiter on both endpoints; click redirect constrained to stored URLs (no open redirect).
- Per-email token signature validated on each hit *(assumed)*.

## Test Checklist

- [ ] Open pixel + click redirect update tracking once.

## UI
- **Kind**: background — open pixel + click-redirect webhook endpoints (guest routes); no interactive page. Tracked opens/clicks surface on the contact/deal timeline.
- **Page**: no dedicated page. Endpoints: `TrackOpenController` (pixel GET) and `TrackClickController` (redirect GET), both guest routes. Results render on `EmailThread` / activity timeline.
- **Layout**: n/a (headless). Open/click badges shown inline on the email thread entry.
- **Key interactions**: none direct — recipient's open/click hits fire the endpoints once (guarded by per-email token *(assumed)*).
- **States**: empty (not yet opened) · loading (n/a) · error (invalid/expired token → 404; open redirect blocked, only stored URLs) · selected (n/a)
- **Gating**: guest routes (no auth) with named rate limiter; tracking data viewable per `crm.email.view`

## Data
- Owns / writes: `crm_emails` (stamps `opened_at` / `clicked_at` once per message; tracking token + wrapped-URL state on the email row)
- Reads: per-email token → email row; stored validated URLs for redirect
- Cross-domain writes: via events only ([[../../../../security/data-ownership]])

## Relations
- Consumes: recipient open/click HTTP hit (guest endpoint)
- Feeds: `EmailTracked` (open/click) → consumed by [[../../activities/_module|crm.activities]] (timeline) and [[../../sales-sequences/_module|crm.sequences]] (engagement signals); `EmailReplied` → sequences auto-halt
- Shared entity: contact/deal timeline (activity rows owned by activities)

## Related

- [[../architecture]] · [[../api]] · [[../security]]
