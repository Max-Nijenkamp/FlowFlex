---
type: module
domain: CRM & Sales
domain-key: crm
panel: crm
module-key: crm.email
status: planned
priority: v1
depends-on: [crm.contacts, crm.activities, core.billing, core.rbac, foundation.queues]
soft-depends: [crm.deals]
fires-events: []
consumes-events: []
patterns: [encryption, queues]
tables: [crm_email_connections, crm_emails]
permission-prefix: crm.email
encrypted-fields: ["crm_email_connections.oauth_token"]
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Email Integration

Bi-directional email sync with Gmail/Outlook. Emails auto-linked to contacts and deals. Send tracked emails from within CRM.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/contacts\|crm.contacts]] | inbound matching by email address |
| Hard | [[domains/crm/activities\|crm.activities]] | emails appear on the activity timeline |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, sync jobs |
| Soft | [[domains/crm/deals\|crm.deals]] | deal-thread linking |

---

## Core Features

- OAuth connection: Gmail / Outlook per user (Google + Microsoft OAuth apps; tokens encrypted)
- Inbound sync: received emails matched to contacts by email address (scheduled pull v1; provider webhooks v1.x *(assumed)*)
- Outbound: send email from CRM via the connected mailbox, logged against the contact/deal
- Email tracking: open tracking (pixel), link click tracking
- Email templates with merge fields (`{{contact.first_name}}` set *(assumed)*)
- Conversation thread view on contact/deal record
- Shared vs private email visibility (per-connection default + per-email override)
- Auto-log: emails appear in the activity timeline

---

## Data Model

### crm_email_connections

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), user_id FK | ulid | unique `(user_id, provider)` |
| provider | string | gmail / outlook |
| 🔐 oauth_token | text | encrypted blob (access+refresh) |
| email_address | string | |
| sync_enabled | boolean default true | |
| default_visibility | string default `shared` | shared / private |
| last_synced_at | timestamp nullable | incremental sync cursor |

### crm_emails

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| connection_id | ulid FK | |
| contact_id / deal_id | ulid nullable FK | matched links |
| direction | string | inbound / outbound |
| subject | string | |
| body | text | HTML purified before storage ([[architecture/security]]) |
| visibility | string | shared / private |
| message_id | string | provider id, unique `(connection_id, message_id)` — sync dedupe |
| thread_id | string nullable | conversation grouping |
| sent_at | timestamp | |
| opened_at / clicked_at | timestamp nullable | tracking |

**Indexes:** `(company_id, contact_id, sent_at)`

GDPR: emails of an erased contact are unlinked + body purged *(assumed — personal correspondence)*.

---

## DTOs

### SendEmailData
| Field | Type | Validation |
|---|---|---|
| contact_id | string | required, has email |
| deal_id | ?string | ulid in company |
| subject | string | required, max:255 |
| body | string | required; purified |
| template_id | ?string | merge fields resolved server-side |
| visibility | string | in:shared,private |

## Services & Actions

- `EmailSyncService::sync(string $connectionId): SyncResult` — incremental from `last_synced_at`; per-message try/catch; dedupe on message_id; match contact by from/to address; logs activity
- `SendTrackedEmailAction::run(SendEmailData $data): EmailData` — sends via provider API, injects pixel + wrapped links, logs activity
- `TrackOpenController` / `TrackClickController` — public pixel/redirect endpoints (no auth, token per email *(assumed)*)
- `DisconnectMailboxAction::run(string $connectionId): void` — revokes token, stops sync, keeps synced mail

**Visibility rule**: private emails readable only by the connection owner (+ nothing else — not even view-any) — query scope enforced.

---

## Filament

**Nav group:** Activities

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `EmailConnectionResource` | #1 CRUD resource (own only) | connect (OAuth redirect), disconnect, visibility default |
| Email thread component | #2 (embedded) | thread view on Contact + Deal pages, visibility-scoped |
| Compose action | modal action | on contact/deal view |

---

## Permissions

`crm.email.connect-own` · `crm.email.send` · `crm.email.view-shared`

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `SyncMailboxesCommand` | default | every 10 min | per-connection cursor + message_id dedupe |
| `SendEmailJob` | notifications | on send | provider message id recorded; retry-safe |

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] OAuth tokens ciphertext in DB; disconnect revokes
- [ ] Sync dedupes on message_id (run twice = no duplicates)
- [ ] Inbound matched to contact by address; unmatched stored unlinked *(assumed)*
- [ ] Private email invisible to other users incl. view-any
- [ ] Body purified (XSS fixture)
- [ ] Open pixel + click redirect update tracking once
- [ ] Provider API mocked (`Http::fake`) — no real calls in tests

---

## Build Manifest

```
database/migrations/xxxx_create_crm_email_connections_table.php
database/migrations/xxxx_create_crm_emails_table.php
app/Models/CRM/{EmailConnection,Email}.php
app/Data/CRM/{SendEmailData,EmailData}.php
app/Services/CRM/EmailSyncService.php
app/Actions/CRM/{SendTrackedEmailAction,DisconnectMailboxAction}.php
app/Http/Controllers/{EmailOAuthController,TrackOpenController,TrackClickController}.php
app/Jobs/CRM/{SyncMailboxJob,SendEmailJob}.php
app/Console/Commands/CRM/SyncMailboxesCommand.php
app/Filament/CRM/Resources/EmailConnectionResource.php
app/Livewire/CRM/EmailThread.php
database/factories/CRM/{EmailConnectionFactory,EmailFactory}.php
tests/Feature/CRM/{EmailSyncTest,EmailVisibilityTest,EmailTrackingTest}.php
```

---

## Related

- [[domains/crm/contacts]]
- [[domains/crm/activities]]
- [[architecture/patterns/encryption]]
- [[architecture/security]]
