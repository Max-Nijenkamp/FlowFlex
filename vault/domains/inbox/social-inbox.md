---
type: module
domain: Omnichannel Inbox
panel: inbox
module-key: inbox.social
status: planned
color: "#4ADE80"
---

# Social Inbox

> Instagram Direct Messages and Facebook Messenger in the shared inbox via Meta Graph API — receive DMs, reply from inbox, story mentions as conversation starters, and comment monitoring on posts.

**Panel:** `/inbox`
**Module key:** `inbox.social`

## What It Does

Social Inbox brings Instagram Direct Messages and Facebook Messenger conversations into the shared inbox alongside email, WhatsApp, and SMS. Agents reply to social DMs from the same workspace without logging into Instagram or Facebook. When a customer mentions a connected Instagram Business account in a story reply, this starts a conversation automatically. The module also monitors comments on Facebook Page and Instagram posts, allowing agents to reply to post comments directly from the inbox without navigating to each social platform. Long-lived page access tokens are encrypted and stored per channel.

## Features

### Core
- Connect Facebook Page via Meta Graph API — requires admin access to the Page + a System User access token
- Connect Instagram Business Account linked to the same Meta Business Manager (Instagram DMs require Instagram Graph API access)
- Inbound Facebook Messenger: receive text messages, images, file attachments, quick replies, and postbacks from Messenger users. Each conversation creates or continues an `inbox_conversation` with `channel_type = facebook`.
- Inbound Instagram DM: receive text messages, images, reels shares, and story replies from Instagram users. Conversations with `channel_type = instagram`.
- Outbound replies: agents send text and image replies from the inbox. Sent via Graph API to the conversation thread using the Page / Instagram account as sender.
- Story mentions: when a user mentions the connected Instagram account in their story, a notification conversation is created so agents can respond to the mention
- Webhook receiver at `/webhooks/meta/social/{verify_token}` handling all Meta social event types

### Advanced
- Comment monitoring: pull comments from connected Facebook Page posts and Instagram feed posts every 5 minutes via Graph API polling. Comments displayed in a "Comments" sub-tab within the social channel view. Agents can reply to comments directly (reply posts on the original post thread via Graph API).
- Message read receipts: `read_at` updated when Meta confirms the Messenger/Instagram message was read by the recipient
- Typing indicators in DM conversations: broadcast to agent inbox when the customer is typing (Meta sends `messaging_typing` webhook events)
- Customer profile enrichment: on new conversation, fetch customer's Facebook or Instagram profile (name, profile picture, follower count where available) and store on the conversation for agent context. Profile picture displayed in the inbox thread header.
- 24-hour messaging window (Messenger): Meta enforces a 24-hour window for Messenger (similar to WhatsApp). Outbound messages outside the window are blocked with a notice — agents prompted to use a Message Tag (e.g. Post-Purchase Update) if applicable. FlowFlex does not automate Message Tag sends — agents select the tag manually.

### AI-Powered
- Social-tone reply drafts: Claude drafts replies calibrated for the informality and brevity of DM conversations vs email
- Comment sentiment triage: AI analyses post comments as they arrive and flags negative-sentiment comments for priority review, helping agents respond to complaints before they escalate publicly

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

    inbox_social_configs {
        ulid id PK
        ulid channel_id FK
        string platform
        string page_id
        string instagram_account_id
        string verify_token
        boolean monitor_comments
        timestamp token_expires_at
        timestamps created_at/updated_at
    }

    inbox_social_comments {
        ulid id PK
        ulid company_id FK
        ulid channel_id FK
        string platform
        string post_id
        string comment_id
        string commenter_name
        text body
        boolean is_replied
        timestamp commented_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `platform` | facebook / instagram |
| `page_id` | Facebook Page ID used for Messenger and Graph API calls |
| `instagram_account_id` | Instagram Business Account ID (distinct from Page ID) |
| `token_expires_at` | Long-lived tokens last ~60 days — a scheduled job refreshes tokens 7 days before expiry |
| `monitor_comments` | if true, `PollSocialComments` scheduled command includes this channel |
| `credentials_encrypted` | Encrypted JSON: `{ page_access_token, instagram_access_token }` |

## Permissions

```
inbox.social.view
inbox.social.send
inbox.social.configure
inbox.social.comments
inbox.social.reports
```

## Filament

- **Resource:** `ChannelResource` (shared)
- **Custom pages:** `SocialChannelSetupWizard` — Step 1 choose platform (Facebook / Instagram), Step 2 Facebook Login OAuth flow (opens Meta OAuth popup, captures Page access token), Step 3 link Instagram account (selects from Instagram accounts connected to the authorised Facebook Page), Step 4 configure webhook and subscribe to webhook events via Graph API, Step 5 comment monitoring settings. Class: `App\Filament\Inbox\Pages\SocialChannelSetupWizard`. The Meta OAuth step uses a temporary state token stored in the user's session to prevent CSRF.
- **Widgets:** `SocialCommentsWidget` — on the Inbox panel dashboard shows count of unresponded post comments across all social channels.
- **Nav group:** Channels (inbox panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Respond.io | Instagram DM + Facebook Messenger inbox |
| Sprout Social | Social inbox, comment monitoring |
| Hootsuite | Inbox, social comment management |
| Agorapulse | Social inbox, comment moderation |
| Chatwoot | Facebook Messenger + Instagram DM channels |

## Related

- [[shared-inbox]]
- [[whatsapp-channel]]
- [[inbox-automations]]
- [[domains/marketing/INDEX]]

## Implementation Notes

- **Meta Webhooks:** Single webhook endpoint `/webhooks/meta/social/{verify_token}` handles events for both Messenger and Instagram DM. Meta sends all Page events to the same webhook. The `MetaSocialWebhookController` inspects the `object` field (`page` or `instagram`) and the entry structure to route events to the correct handler (`MessengerHandler` or `InstagramHandler`).
- **Instagram DM permission requirements:** Instagram DM access requires the app to have `instagram_manage_messages` permission, which requires Meta's manual app review. The setup wizard includes instructions for requesting this permission in Meta App Review.
- **Page access token lifecycle:** Long-lived page access tokens (60-day expiry from Meta) are stored encrypted. A `RefreshSocialTokens` scheduled daily command checks `token_expires_at` for all social channels. When < 7 days remain, it exchanges the current token for a new long-lived token via `GET /oauth/access_token?grant_type=fb_exchange_token`. If refresh fails, sends a Filament notification to company admin to re-authenticate.
- **Comment polling:** `PollSocialComments` command runs every 5 minutes. For each active social channel with `monitor_comments = true`, it queries the Graph API for recent comments on the Page's recent posts (`GET /{page_id}/feed?fields=comments{...}`). New comments (not in `inbox_social_comments` by `comment_id`) are stored. Agents receive real-time Reverb notification when new comments arrive.
- **Outbound via Graph API:** `FacebookAdapter::send()` and `InstagramAdapter::send()` call the Graph API `POST /{page_id}/messages` (Messenger) or `POST /{ig_account_id}/messages` (Instagram) with the reply payload. Both adapters wrap in try/catch and handle `OAuthException` (token expired) by triggering a Filament admin alert.
