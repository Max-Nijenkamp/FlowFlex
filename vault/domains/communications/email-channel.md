---
type: module
domain: Communications
panel: comms
module-key: comms.email
status: planned
color: "#4ADE80"
---

# Email Channel

Connect a company email inbox (support@, info@) so inbound emails become shared-inbox conversations and replies send from that address.

## Core Features

- Connect inbox: OAuth (Gmail/Outlook) or email forwarding to a FlowFlex address
- Inbound: incoming emails parsed into conversations in the shared inbox
- Outbound: replies sent from the connected address, threaded correctly
- Email signature per channel
- Attachment handling: inbound attachments stored, outbound attachments sent
- Threading: match replies to existing conversations via References/In-Reply-To headers
- HTML and plain-text email support
- Spam filtering (basic)

## Data Model

| Table | Key Columns |
|---|---|
| `comms_email_channels` | company_id, channel_id, address, connection_type (oauth/forward), oauth_token (encrypted), signature |

Messages flow through `comms_messages` (channel_type = email).

## Filament

**Nav group:** Settings

- `EmailChannelResource` — connect, configure signature, test connection
- Inbound processing via queued job parsing the incoming mail

## Cross-Domain / Security

- OAuth tokens encrypted (see [[architecture/patterns/encryption]])
- Inbound mail via webhook or IMAP polling job

## Related

- [[domains/communications/shared-inbox]]
- [[architecture/email]]
