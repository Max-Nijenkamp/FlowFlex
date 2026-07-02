---
domain: crm
module: email-integration
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Email Integration — API / DTOs

## Input DTO — `SendEmailData`

| Field | Type | Rules |
|---|---|---|
| `contact_id` | ulid | required; contact must have an email |
| `deal_id` | ulid nullable | must be in company |
| `subject` | string | required, `max:255` |
| `body` | text | required; HTML purified |
| `template_id` | ulid nullable | merge fields resolved server-side |
| `visibility` | string | `in:shared,private` |

## Output DTO — `EmailData`

Returned by `SendTrackedEmailAction::run()` — the persisted `crm_emails` row projected out (id, direction, subject, body, visibility, `message_id`, `thread_id`, `sent_at`, tracking timestamps, and the linked contact/deal ids).

## Public / Portal Endpoints

These run outside the authenticated app session (guest guard) — see [[../../../security/authn-authz]].

| Controller | Auth | Purpose |
|---|---|---|
| `EmailOAuthController` | user (redirect flow) | Handles the Google/Microsoft OAuth callback; verifies `state` + PKCE before persisting the encrypted token. See [[../../../security/webhooks-signing]]. |
| `TrackOpenController` | none (guest) | Serves the open-tracking pixel; validates a per-email token signature *(assumed)*; stamps `opened_at` once. |
| `TrackClickController` | none (guest) | Click redirect; validates the per-email token; redirects only to validated stored URLs; stamps `clicked_at` once. |

Tracking endpoints run on a guest route group isolated from authenticated guards and are protected by a named rate limiter — see [[security]].
