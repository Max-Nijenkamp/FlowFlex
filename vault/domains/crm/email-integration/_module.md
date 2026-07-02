---
domain: crm
module: email-integration
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# CRM Email Integration

Planned bi-directional email sync with Gmail/Outlook. Emails are auto-linked to contacts and deals, and tracked emails can be sent from within the CRM.

> This module is planned for rebuild. Prior "shipped/complete" references reflect the stripped codebase; see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] for context.

## Module-key

| Field | Value |
|---|---|
| key | `crm.email` |
| priority | v1 |
| panel | crm |
| permission-prefix | `crm.email` |
| tables | `crm_email_connections`, `crm_emails` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../contacts/_module\|Contacts]] | Inbound matching by email address |
| Hard | [[../activities/_module\|Activities]] | Emails appear on the activity timeline |
| Hard | [[../../core/billing/_module\|Billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|RBAC]] | Permissions, `canAccess()` |
| Hard | [[../../foundation/queues/_module\|Queues]] | Sync + send jobs |
| Soft | [[../deals/_module\|Deals]] | Deal-thread linking |

## Core Features

- **OAuth connection** — Gmail/Outlook per user via Google + Microsoft OAuth apps; tokens encrypted.
- **Inbound sync** — received emails matched to contacts by email address (scheduled pull for v1; provider webhooks in v1.x *(assumed)*).
- **Outbound send** — from CRM via the connected mailbox, logged against contact/deal.
- **Email tracking** — open pixel + link-click tracking.
- **Email templates** — merge fields (`{{contact.first_name}}` syntax *(assumed)*).
- **Conversation thread view** — on contact and deal pages.
- **Shared vs private visibility** — per-connection default with per-email override.
- **Auto-log** — emails logged on the activity timeline.

## See features/

- [[features/oauth-connection|OAuth Connection]] — connecting a Gmail/Outlook mailbox.
- [[features/inbound-sync|Inbound Sync]] — scheduled incremental pull + contact matching.
- [[features/email-tracking|Email Tracking]] — open pixel + click redirect.

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

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] OAuth tokens stored as ciphertext in DB; disconnect revokes.
- [ ] Sync dedupes on `message_id` (run twice = no duplicates).
- [ ] Inbound matched to contact by address; unmatched stored unlinked *(assumed)*.
- [ ] Private email invisible to other users including view-any.
- [ ] Body purified (XSS fixture).
- [ ] Open pixel + click redirect update tracking once.
- [ ] Provider API mocked (`Http::fake`) — no real calls in tests.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | contact read API | [[../contacts/_module\|crm.contacts]] | match sender/recipient by email address |
| Fires | `EmailTracked` (open/click) | [[../activities/_module\|crm.activities]], [[../sales-sequences/_module\|crm.sequences]], revenue-intelligence | engagement signal on timeline |
| Fires | `EmailReplied` | [[../sales-sequences/_module\|crm.sequences]], [[../activities/_module\|crm.activities]] | sequences auto-halt on reply |
| Consumes | OAuth handshake / API | core/integrations provider *(assumed)* | provider consent + token refresh |

**Data ownership:** `crm.email` writes only `crm_email_connections`, `crm_emails`; all cross-domain effects go through events / owning-service APIs ([[../../../security/data-ownership]]).

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../contacts/_module|Contacts]] · [[../activities/_module|Activities]] · [[../deals/_module|Deals]]
- [[../../../architecture/patterns/encryption]] · [[../../../glossary]]
