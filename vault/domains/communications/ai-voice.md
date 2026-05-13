---
type: module
domain: Communications
panel: comms
module-key: comms.ai-voice
status: planned
color: "#4ADE80"
---

# AI Voice

> A 24/7 AI phone receptionist that answers calls, routes by intent, books appointments, takes transcribed voicemails, and hands off to human agents — replacing expensive phone answering services.

**Panel:** `/comms`
**Module key:** `comms.ai-voice`

## What It Does

AI Voice gives every FlowFlex company a programmable phone system with an AI receptionist as the default handler. Inbound calls are answered immediately — the AI identifies caller intent through natural conversation, answers common questions by searching the knowledge base, books appointments directly into the CRM scheduling system, takes voicemails that are transcribed and emailed, and transfers to a human agent when needed. Every call is recorded (with consent prompt), transcribed, and summarised. Outbound call campaigns allow AI-initiated follow-up calls with automatic handoff to a human at the moment the prospect shows interest.

## Features

### Core
- Phone number provisioning: claim one or more inbound phone numbers (local or toll-free) via the telephony provider — numbers are listed in `comms_phone_numbers` and linked to the company account
- Inbound call handling: all inbound calls to provisioned numbers are handled by the AI agent — the AI greets callers, asks how it can help, and routes based on detected intent
- Intent routing: configurable routing rules (`comms_call_routing_rules`) — conditions on time of day, detected intent keywords, or caller history determine the action: answer with knowledge base lookup, transfer to a specific user or department, go to voicemail, or book an appointment
- AI knowledge base answers: the AI searches `kb_articles` (Support domain) in real time during the call and synthesises a natural-language answer — handles FAQs, pricing questions, hours, and directions without human involvement
- Voicemail: when no agent is available (outside hours, all busy, or caller preference), the AI prompts for a voicemail — voicemail audio is transcribed by Deepgram and emailed to the configured recipient with the transcript and a playback link
- Call transcription: every call (AI-handled and human-transferred) is transcribed by Deepgram and stored in `comms_calls.transcript`
- Call summary: GPT-4o generates a structured call summary (intent, key questions, outcomes, action items) stored in `comms_calls.summary` — shown on the call detail page

### Advanced
- Appointment booking: the AI can book appointments directly during the call by accessing the CRM scheduling module's availability API — it proposes available slots conversationally and confirms the booking, creating a `crm_bookings` record and sending a confirmation email without human involvement
- Human transfer: at any point in the call, the AI can transfer to a specific user or a ring group — the AI provides the human agent with a brief handoff context (caller name, intent, what was discussed) read aloud or via screen pop in the Filament panel
- Call analytics dashboard: call volume by day/hour, AI resolution rate (calls handled end-to-end by AI without transfer), missed call rate, average call duration, voicemail volume — displayed on the `CallAnalyticsPage`
- Business hours configuration: define operating hours per day; outside hours the AI uses a different routing rule set (typically voicemail or a friendly "we're closed" message with hours)
- Outbound call campaigns: define a list of contacts and a call script; the AI makes outbound calls, delivers the opening, and immediately transfers to a human agent if the contact responds positively — prevents robocall feel while ensuring human closes the conversation

### AI-Powered
- Voice synthesis: all AI speech uses ElevenLabs or Google Cloud Text-to-Speech for natural, human-like delivery — company can choose a voice persona name and voice style in settings
- Speech recognition: Deepgram (Nova-2 model, recommended for low latency) transcribes caller speech in real time — chosen over Whisper for sub-300ms streaming latency required for natural conversation
- Intent classification: GPT-4o classifies caller intent from the first 2–3 exchanges — maps to a predefined intent taxonomy (book appointment / get information / billing question / complaint / sales inquiry / other) and triggers the corresponding routing rule
- Sentiment analysis: caller sentiment is tracked throughout the call — escalating negative sentiment auto-triggers a human transfer regardless of routing rules
- Post-call CRM update: after each call, AI updates the linked CRM contact with a call activity log entry including summary, intent classification, and any booked appointments

## Data Model

```erDiagram
    comms_phone_numbers {
        ulid id PK
        ulid company_id FK
        string number
        enum provider
        json capabilities
        boolean is_active
        timestamps created_at/updated_at
    }

    comms_calls {
        ulid id PK
        ulid company_id FK
        ulid phone_number_id FK
        enum direction
        string caller_number
        integer duration_seconds
        enum status
        text transcript
        text summary
        string recording_url
        boolean ai_handled
        ulid transferred_to FK "nullable"
        ulid crm_contact_id FK "nullable"
        enum intent_classification
        enum caller_sentiment
        timestamp started_at
        timestamps created_at/updated_at
    }

    comms_call_routing_rules {
        ulid id PK
        ulid company_id FK
        integer priority
        json conditions
        enum action
        json action_params
        boolean is_active
        timestamps created_at/updated_at
    }

    comms_voicemails {
        ulid id PK
        ulid call_id FK
        ulid company_id FK
        string audio_url
        text transcript
        ulid notified_user_id FK
        boolean is_read
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `comms_phone_numbers.provider` | enum: `twilio` / `vonage` |
| `comms_phone_numbers.capabilities` | JSON: `{voice: true, sms: true, fax: false}` |
| `comms_calls.direction` | enum: `inbound` / `outbound` |
| `comms_calls.status` | enum: `completed` / `missed` / `voicemail` / `transferred` / `failed` |
| `comms_calls.intent_classification` | enum: `appointment` / `information` / `billing` / `complaint` / `sales` / `other` / `unknown` |
| `comms_calls.caller_sentiment` | enum: `positive` / `neutral` / `negative` / `escalating` |
| `comms_call_routing_rules.conditions` | JSON: `{time_range: {start: "09:00", end: "17:00"}, days: ["mon","tue",...], intent: ["appointment"], ...}` |
| `comms_call_routing_rules.action` | enum: `ai_answer` / `transfer_user` / `transfer_group` / `voicemail` / `book_appointment` / `play_message` |

## Permissions

```
comms.ai-voice.view-calls
comms.ai-voice.manage-numbers
comms.ai-voice.manage-routing
comms.ai-voice.run-outbound-campaigns
comms.ai-voice.access-recordings
```

## Filament

- **Resource:** `CallResource` — paginated list of all calls (inbound and outbound) with columns: direction, caller number, duration, status, AI handled badge, intent tag, started at; click to open call detail page showing full transcript, summary, sentiment timeline, and linked CRM contact
- **Resource:** `CallRoutingResource` — CRUD for routing rules with a priority-ordered list and a drag-to-reorder handle; each rule shows conditions summary and action in plain English (e.g. "Business hours + intent: appointment → Book appointment via CRM Scheduling")
- **Custom page:** `CallAnalyticsPage` — tabs for Overview (call volume chart, AI resolution rate KPI, missed call rate), Intent Breakdown (pie chart of intent classifications), and Voicemails (unread voicemail queue with play button and transcript)
- **Nav group:** Broadcast (comms panel) — positioned as an outward-facing channel alongside email broadcasts
- **Widget:** `ActiveCallWidget` — real-time indicator on the comms dashboard showing whether any calls are currently in progress (uses Reverb WebSocket `presence-calls.{company_id}` channel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Smith.ai | AI call answering and appointment booking service |
| Ruby Receptionists | Virtual receptionist (human-staffed) |
| My AI Front Desk | AI phone receptionist for SMBs |
| Marlie AI | AI voice agent for inbound calls |
| Dialpad AI | AI-powered business phone system |
| Twilio Studio (DIY) | Custom call flow builder — replaced with no-code routing rules |

## Related

- [[../crm/appointment-scheduling]]
- [[notification-center]]
- [[messaging]]
- [[../support/INDEX]]

## Implementation Notes

### Telephony Provider
**Recommended provider: Twilio Voice API.** Twilio has the most mature PHP SDK, the broadest global number availability, and the lowest per-minute cost at scale ($0.0085/min outbound US). Vonage is the recommended fallback for EU local number coverage where Twilio is less competitive. The `provider` enum on `comms_phone_numbers` allows both to coexist — a company can have one Twilio US number and one Vonage DE number.

Twilio integration uses TwiML for call flow control. The AI agent runs as a webhook endpoint that Twilio calls on each speech input turn. The webhook URL is `POST /api/v1/comms/voice/twilio/{company_ulid}`.

### Real-Time Conversation Architecture
The AI voice loop is: Twilio calls the webhook → PHP controller receives speech transcript from Twilio → GPT-4o generates next response → ElevenLabs or Google TTS synthesises audio → PHP returns TwiML `<Play>` with audio URL → Twilio plays audio to caller. This full round trip must complete in under 2 seconds for acceptable conversational flow. Optimisations:
- Use streaming TTS endpoints (ElevenLabs streaming) to begin playback before synthesis is complete
- Cache common greetings and FAQ responses as pre-synthesised audio files in Cloudflare CDN
- Use GPT-4o with a max 150-token response limit for routing decisions — brevity reduces latency

### Deepgram vs Whisper
Deepgram Nova-2 provides ~270ms transcription latency vs ~800ms for OpenAI Whisper (non-streaming). For real-time conversation, Deepgram is non-negotiable. Whisper can be used for post-call full transcript generation where latency is not critical. Document this decision in an ADR.

### Recording and Consent
Call recording requires two-party consent in many US states and EU jurisdictions. The AI's opening greeting must include: "This call may be recorded for quality and training purposes." This is non-optional and must run before any routing logic. Recording can be toggled off per company in settings — when off, the `recording_url` field is null and transcription is still performed (via streaming, without storing audio).

### Outbound Campaign Compliance
Outbound AI calls to consumer numbers are regulated under TCPA (US) and PECR (UK). The outbound campaign feature must include: (1) an opt-out mechanism during the call ("Press 9 to be removed from future calls"), (2) a maximum call frequency per contact (default: once per 30 days), (3) time-of-day restrictions (8am–9pm local time, enforced using contact timezone from CRM). Legal disclaimer on the campaign creation form reminding HR to ensure consent.
