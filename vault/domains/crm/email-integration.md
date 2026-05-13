---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.email
status: planned
color: "#4ADE80"
---

# Email Integration

> Bi-directional email sync — emails sent and received are automatically linked to the matching contact record, creating a complete communication history without manual logging.

**Panel:** `crm`
**Module key:** `crm.email`

## What It Does

Email Integration connects each sales rep's email account (Google Workspace or Microsoft 365) to FlowFlex CRM. Emails sent to or received from a contact's email address are automatically associated with that contact record and appear in their activity timeline. Sales reps no longer need to manually log emails. Shared templates and tracked opens let the team see when a prospect reads an email. The integration runs as a background sync job — emails from the last 30 days are imported on first connect, and new emails sync in near real-time thereafter.

## Features

### Core
- OAuth connection: Google Workspace or Microsoft 365 — one connection per user, authorised via OAuth2 consent
- Auto-link: incoming and outgoing emails matched by sender/recipient email against `crm_contacts` — linked as email-type activities
- Timeline appearance: linked emails appear in the contact's activity feed with subject, snippet, and timestamp
- Compose from FlowFlex: send an email to a contact directly from the contact record — goes via the connected email account
- Sync status: per-user sync health shown in settings — last sync time, error count, total emails synced

### Advanced
- Email templates: shared team email templates with merge fields ({first_name}, {company}, {deal_value}) — usable when composing from FlowFlex
- Open and click tracking: pixel-based open tracking on emails sent from FlowFlex — "opened" and "clicked link" events logged as micro-activities on the contact
- Thread view: email replies grouped into threads on the contact timeline — mirrors Gmail/Outlook threading
- Shared inbox: optional team-shared inbox for generic addresses (info@, sales@) — emails routed to the correct rep based on contact ownership
- BCC to CRM: fallback for reps who prefer to log manually — BCC a unique FlowFlex address; email auto-filed to the matching contact

### AI-Powered
- Reply suggestions: when an email is received from a contact, AI drafts three suggested reply starters based on the email content and the deal context
- Sentiment analysis: AI classifies incoming emails as positive / neutral / negative — negative sentiment emails in active deals surface as an alert to the deal owner

## Data Model

```erDiagram
    crm_email_connections {
        ulid id PK
        ulid user_id FK
        ulid company_id FK
        string provider
        string email_address
        string access_token
        string refresh_token
        timestamp token_expires_at
        timestamp last_synced_at
        string status
        timestamps created_at/updated_at
    }

    crm_email_messages {
        ulid id PK
        ulid company_id FK
        ulid contact_id FK
        ulid deal_id FK
        ulid user_id FK
        string message_id "unique per provider"
        string subject
        string snippet
        string direction
        timestamp sent_at
        boolean is_opened
        timestamp opened_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `provider` | google / microsoft |
| `direction` | inbound / outbound |
| `message_id` | Provider's unique message ID — prevents duplicate sync |

## Permissions

- `crm.email.connect-account`
- `crm.email.view-own-emails`
- `crm.email.view-team-emails`
- `crm.email.send`
- `crm.email.manage-templates`

## Filament

- **Resource:** `EmailTemplateResource`
- **Pages:** `ListEmailTemplates`, `EmailConnectionSettingsPage`
- **Custom pages:** None
- **Widgets:** `EmailSyncStatusWidget` — per-user sync health on CRM settings page
- **Nav group:** Activities (crm panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| HubSpot Email Integration | CRM email tracking and sync |
| Salesforce Inbox | Email-to-CRM integration |
| Outreach | Email tracking and sales engagement |
| Mixmax | Gmail CRM integration |

## Implementation Notes

**External dependency — Google & Microsoft OAuth:** The sync integration uses two separate OAuth2 flows:
1. **Google Workspace:** Use `google/apiclient` PHP package. OAuth consent grants the `gmail.readonly` and `gmail.send` scopes. Sync runs via the Gmail API `users.messages.list` + `users.messages.get` endpoints in a queued job (`SyncGmailJob`). Store `access_token` and `refresh_token` in `crm_email_connections`. Token refresh is handled by Google's client library automatically when the access token expires.
2. **Microsoft 365:** Use Microsoft Graph API. OAuth consent grants `Mail.Read` and `Mail.Send` delegated permissions. Sync via `GET /me/messages` with a `$filter` on sender/recipient email addresses. Store tokens in `crm_email_connections`. Token refresh via `refresh_token` grant.

Both integrations require an OAuth app registration in Google Cloud Console and Azure Portal respectively. App credentials go in `config/services.php` as `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `MICROSOFT_CLIENT_ID`, `MICROSOFT_CLIENT_SECRET`.

**Sync job pattern:** `SyncEmailConnectionJob` is a queued job that runs every 5 minutes per active connection (dispatched by a scheduler loop or by a per-user scheduled task). For initial sync it fetches the last 30 days; for incremental sync it uses a `historyId` (Gmail) or `$deltaToken` (Graph API) to fetch only new messages since last sync. Jobs are queued on the `emails` queue with a timeout of 120 seconds.

**Pixel tracking:** Open tracking uses a 1×1 transparent PNG served by a FlowFlex public route (`/t/{email_event_token}.png`). The controller that serves this image records the open event as a `crm_email_messages` update (`is_opened = true, opened_at = now()`). Click tracking wraps outbound links with a redirect via `/c/{email_event_token}?url={encoded_url}`.

**Security:** `crm_email_connections.access_token` and `refresh_token` must be encrypted at rest. Use Laravel's `encrypted` cast on the model: `protected $casts = ['access_token' => 'encrypted', 'refresh_token' => 'encrypted']`.

**AI features:** Reply suggestions and sentiment analysis call `app/Services/AI/EmailInsightService.php` wrapping OpenAI GPT-4o. Reply suggestions use the email thread as context. Sentiment analysis uses a structured classification prompt returning `{sentiment: positive|neutral|negative, confidence: float}`.

## Related

- [[contacts]]
- [[activities]]
- [[sales-sequences]]
- [[revenue-intelligence]]
