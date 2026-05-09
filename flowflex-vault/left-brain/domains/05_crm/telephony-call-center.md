---
type: module
domain: CRM & Sales
panel: crm
cssclasses: domain-crm
phase: 3
status: planned
migration_range: 250000–299999
last_updated: 2026-05-09
---

# Telephony & Call Center Integration

CTI (Computer Telephony Integration) — make and receive calls directly from CRM. Every call logged, recorded, transcribed, and linked to contact/deal. Replaces Aircall, JustCall, Cloudtalk, Dialpad, Five9.

**Panel:** `crm`  
**Phase:** 3 — core CRM capability; sales and support teams use phones from day one

---

## Features

### Click-to-Call
- Click any phone number in CRM → auto-dial via browser (WebRTC softphone)
- Works from contact record, deal record, ticket record, inbox
- Outbound caller ID: company number or individual DDI
- International dialling with prefix auto-format

### Inbound Call Handling
- Incoming call → lookup caller number against contacts database
- Screen pop: matching contact/deal record opens automatically
- IVR routing (press 1 for sales, 2 for support — routes to queue)
- Queue management: show queue depth, average wait time
- Voicemail capture with auto-transcription
- Call forwarding rules (out of hours → voicemail / mobile)
- Business hours enforcement per number

### Call Recording & Transcription
- All calls recorded (configurable per direction: inbound/outbound/both)
- GDPR disclosure: auto-play "this call may be recorded" message
- Transcription via Whisper / Deepgram
- Transcript stored on contact/deal record
- Keyword search across all transcripts
- Redaction: automatically redact credit card numbers from transcripts

### Call Logging
- Every call (answered, missed, voicemail) auto-logged as activity on contact
- Duration, direction, outcome (answered/no-answer/voicemail)
- Call notes: agent adds notes during/after call
- Disposition tagging: "interested", "callback", "not qualified", "wrong number"
- Next action creation from call (create task, schedule callback)

### AI Call Intelligence (Phase 6 extension)
- AI call summary (3-sentence summary posted to CRM after call ends)
- Action item extraction ("I'll send the proposal by Friday" → auto-creates task)
- Sentiment analysis per call
- Talk/listen ratio per rep
- Coaching: flag calls where rep talked >70% of time
- Objection detection: highlight moments when prospect raised pricing/timing objections

### Number Management
- Rent local/toll-free numbers in 50+ countries (via Twilio/Vonage)
- Port existing numbers
- DDIs (Direct Dial In) per sales rep
- Shared team numbers (sales, support, billing)
- Number pool for outbound campaigns

### Power Dialler (Sales Teams)
- Upload list of leads → auto-dial sequentially
- Skip after N rings
- Auto-advance to next number after call disposition
- Pause/resume dialling session
- GDPR/TCPA compliance checks (do-not-call list matching before dial)

### Integrations
- Twilio (primary telephony provider)
- Vonage Business (alternative)
- Aircall (if customer already has Aircall — sync call logs)
- Zoom Phone, Microsoft Teams Calling (for teams using those platforms)

---

## Data Model

```erDiagram
    calls {
        ulid id PK
        ulid company_id FK
        string direction
        string status
        string from_number
        string to_number
        ulid contact_id FK
        ulid deal_id FK
        ulid ticket_id FK
        ulid agent_id FK
        integer duration_seconds
        string recording_url
        string transcript_text
        json ai_summary
        string disposition
        timestamp started_at
        timestamp ended_at
    }

    phone_numbers {
        ulid id PK
        ulid company_id FK
        string number
        string country_code
        string type
        string provider
        ulid assigned_to FK
        json routing_rules
        boolean is_active
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `CallCompleted` | Call hangs up | CRM (log activity), AI (transcribe), Notifications (missed call alert) |
| `CallMissed` | Inbound not answered | Notifications (agent + manager) |
| `VoicemailReceived` | Caller leaves voicemail | Notifications (agent), CRM (log with transcript) |

---

## Permissions

```
crm.telephony.make-calls
crm.telephony.view-recordings
crm.telephony.manage-numbers
crm.telephony.view-team-calls
crm.telephony.access-ai-insights
```

---

## Competitors Displaced

Aircall · JustCall · Cloudtalk · Dialpad · Vonage Contact Center · Five9 · NICE inContact

---

## Related

- [[MOC_CRM]]
- [[entity-contact]]
- [[MOC_Communications]] — internal voice calls use different stack
- [[MOC_AI]] — AI Meeting Intelligence shares transcription infrastructure
