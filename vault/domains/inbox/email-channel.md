---
type: module
domain: Omnichannel Inbox
panel: inbox
module-key: inbox.email
status: planned
color: "#4ADE80"
---

# Email Channel

> Connect support email addresses to the shared inbox via Mailgun/SES inbound routing or IMAP polling. Email threading, HTML rendering, attachment support, and multiple inboxes per company.

**Panel:** `/inbox`
**Module key:** `inbox.email`

## What It Does

Email Channel connects one or more company email addresses (support@, sales@, billing@) to the shared inbox so every inbound email becomes a conversation that agents manage in the same workspace as WhatsApp and SMS. Replies sent by agents from the inbox are delivered from the connected email address — the customer sees a normal email conversation. Email threading uses `In-Reply-To` and `References` headers to match customer replies to the correct existing conversation, keeping the full thread intact. HTML emails are rendered safely in the conversation view. File attachments are stored via spatie/laravel-media-library and displayed inline. Companies with multiple email addresses can connect each as a separate inbox channel, labelled and filterable.

## Features

### Core
- Connect email address via Mailgun inbound routing or Amazon SES receipt rules — inbound emails POST to a FlowFlex webhook
- Fallback: IMAP polling via `webklex/laravel-imap` for email providers that cannot configure webhooks (e.g. Gmail, Outlook with app password)
- Email threading: match inbound emails to existing conversations using `In-Reply-To` and `References` headers against stored `email_message_id` values
- HTML email rendering: inbound HTML emails sanitised via HTMLPurifier and rendered in the conversation thread. Plain-text emails displayed as preformatted text.
- Attachments: inbound email attachments stored via spatie/laravel-media-library and displayed as downloadable thumbnails in the thread
- Outbound: replies composed in the inbox are sent via the connected sending provider (Mailgun/SES) from the `From` address matching the inbox channel email address. `Reply-To` set to the channel address so customer replies route back correctly.
- Multiple email channels per company: each connected address is a separate `inbox_channel` record. Conversations are tagged by which address they arrived on.

### Advanced
- Auto-reply on first contact: optional automated first-response email sent immediately when a new conversation is created (acknowledges receipt, provides ticket reference)
- Signature per channel: HTML email signature appended to all outbound replies from a given channel. Configurable per channel in Filament.
- CC/BCC support: agents can CC additional recipients on outbound replies. CC addresses stored on the message record and included in the MIME headers.
- Email blocking: block inbound emails from specific addresses or domains (creates no conversation — silently discarded)
- Bounce handling: Mailgun/SES delivery failure webhooks update `inbox_messages.delivery_status` and alert the agent with a Filament notification
- Large attachment handling: attachments over 10 MB stored in S3 and replaced with a secure download link in the rendered thread rather than inline display
- Spam detection: Mailgun's spam score flag (`X-Mailgun-Sflag`) used to auto-label or discard high-confidence spam before creating a conversation

### AI-Powered
- Auto-categorisation: Claude reads the email subject and first paragraph and suggests a label and assignee based on historical patterns
- Thread summarisation: for long email conversations (> 10 messages), AI generates a 2-sentence summary shown at the top of the thread to help new agents catch up quickly
- Reply drafting: Claude generates a suggested reply draft based on the email content and any linked knowledge base articles

## Data Model

```erDiagram
    inbox_channels {
        ulid id PK
        ulid company_id FK
        string type
        string name
        json credentials_encrypted
        boolean is_active
        string webhook_secret
        timestamps created_at/updated_at
    }

    inbox_email_configs {
        ulid id PK
        ulid channel_id FK
        string email_address
        string display_name
        string provider
        string inbound_method
        string imap_host
        integer imap_port
        string imap_username
        string imap_password_encrypted
        text html_signature
        boolean auto_reply_enabled
        string auto_reply_subject
        text auto_reply_body
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `provider` | mailgun / ses / smtp — determines outbound sending client |
| `inbound_method` | webhook / imap — determines how inbound emails arrive |
| `imap_password_encrypted` | encrypted with `encrypt()` — only populated when `inbound_method = imap` |
| `auto_reply_enabled` | if true, a queued job sends the `auto_reply_body` on new conversation creation |
| Conversations use `inbox_conversations` with `channel_type = email` | Messages use `inbox_messages` with `external_id` storing the raw email `Message-ID` header |

## Permissions

```
inbox.email.view
inbox.email.send
inbox.email.configure
inbox.email.manage-blocking
inbox.email.reports
```

## Filament

- **Resource:** `ChannelResource` (shared with other channel types — see WhatsApp Channel notes)
- **Pages:** `ListChannels`, `CreateChannel`, `EditChannel`
- **Custom pages:** `EmailChannelSetupWizard` — multi-step wizard: Step 1 enter email address and display name, Step 2 choose inbound method (webhook shows Mailgun/SES instructions with the inbound webhook URL to configure; IMAP asks for IMAP credentials with a "Test connection" button), Step 3 configure outbound sending credentials, Step 4 configure signature and auto-reply. Class: `App\Filament\Inbox\Pages\EmailChannelSetupWizard`.
- **Widgets:** None specific — channel health shown in shared `ChannelResource` list and `InboxSummaryWidget`.
- **Nav group:** Channels (inbox panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Helpscout | Shared email inbox, email threading |
| Freshdesk | Email channel, ticket creation from email |
| Zendesk | Email channel, support address routing |
| Front | Collaborative email inbox |
| Missive | Team email inbox |

## Related

- [[shared-inbox]]
- [[whatsapp-channel]]
- [[sms-channel]]
- [[inbox-automations]]
- [[domains/support/support-tickets]]

## Implementation Notes

- **Mailgun inbound webhook:** Configure in Mailgun Dashboard → Receiving → Create Route → forward to `https://app.flowflex.io/webhooks/email/inbound/{channel_webhook_secret}`. The `EmailInboundController` parses the multipart form POST from Mailgun (which includes headers, body text, body HTML, and attachments). MIME parsing uses PHP's native `imap_*` functions via `webklex/laravel-imap` even for webhook payloads (parse-only mode, no IMAP connection required).
- **Email threading:** On each inbound email, `EmailThreadMatcher` service extracts the `In-Reply-To` and `References` headers. Queries `inbox_messages.external_id` for a match. If found, appends the new message to the existing conversation. If not found (or headers absent), creates a new conversation. Stores the inbound email's own `Message-ID` as the new message's `external_id`.
- **HTML sanitisation:** Inbound HTML bodies pass through `HTMLPurifier` with a config that allows standard formatting elements (p, strong, em, ul, ol, li, a, img, table) but strips scripts, event handlers, and external tracking pixels. Sanitised HTML stored in `inbox_messages.body`. Rendered in a sandboxed `<iframe>` in the Inbox thread UI to fully isolate CSS.
- **IMAP polling:** For channels with `inbound_method = imap`, a `PollImapInbox` scheduled command runs every 2 minutes per active IMAP channel. Uses `webklex/laravel-imap` to connect, fetch unseen messages since last poll, process each through the same `EmailInboundController` logic, and mark messages as seen in IMAP. Last polled UID stored in channel config to avoid reprocessing.
- **Outbound sending:** The `EmailAdapter::send(InboxMessage $message)` method builds a `Mailable` with the reply body, signature, and correct `From`, `Reply-To`, `In-Reply-To`, and `References` headers, then dispatches via `Mail::send()` using the channel-specific Mailgun/SES credentials loaded from `credentials_encrypted`.
