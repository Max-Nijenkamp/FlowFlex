---
type: module
domain: Events Management
panel: events
module-key: events.speakers
status: planned
color: "#4ADE80"
---

# Speakers

> Speaker management — bio and headshot, session assignment, AV requirements, and travel and accommodation notes.

**Panel:** `events`
**Module key:** `events.speakers`

---

## What It Does

Speakers manages all the people presenting or facilitating at an event. Each speaker has a profile record with their name, organisation, bio, and headshot — data that can feed the public event agenda page. Speakers are assigned to specific sessions with their topic, presentation format, and AV requirements. The module also tracks travel and accommodation arrangements so the events team can coordinate logistics in one place.

---

## Features

### Core
- Speaker profile: name, organisation, job title, bio, headshot photo
- Session assignment: link a speaker to an event session with topic, format (keynote, panel, workshop), and duration
- AV requirements: projector, lavalier mic, presentation clicker, slide format (16:9/4:3)
- Bio and headshot portal: send a speaker a link to submit their own bio and headshot
- Speaker list view: all speakers for an event with session assignments

### Advanced
- Travel arrangements: flight details, accommodation name, arrival and departure dates
- Dietary requirements: meal preference for event catering
- Green room notes: special requirements or hospitality instructions for the events team
- Speaker contract status: track whether the speaker agreement has been signed
- Multi-event speakers: one speaker record reusable across multiple events

### AI-Powered
- Bio editing assistant: AI polishes or shortens a submitted bio to match the event programme style
- Speaker gap detection: identify topic or format gaps in the agenda that need additional speakers
- Introduction script: AI drafts a MC introduction for each speaker from their bio and session topic

---

## Data Model

```erDiagram
    speakers {
        ulid id PK
        ulid company_id FK
        string name
        string organisation
        string job_title
        text bio
        string headshot_url
        timestamps created_at_updated_at
    }

    event_speaker_assignments {
        ulid id PK
        ulid event_id FK
        ulid speaker_id FK
        string session_title
        string format
        integer duration_minutes
        json av_requirements
        text travel_notes
        string accommodation
        boolean contract_signed
        timestamps created_at_updated_at
    }

    speakers ||--o{ event_speaker_assignments : "assigned to"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `speakers` | Speaker profiles | `id`, `company_id`, `name`, `organisation`, `bio`, `headshot_url` |
| `event_speaker_assignments` | Session assignments | `id`, `event_id`, `speaker_id`, `session_title`, `format`, `duration_minutes`, `contract_signed` |

---

## Permissions

```
events.speakers.view
events.speakers.create
events.speakers.update
events.speakers.delete
events.speakers.manage-assignments
```

---

## Filament

- **Resource:** `App\Filament\Events\Resources\SpeakerResource`
- **Pages:** `ListSpeakers`, `CreateSpeaker`, `EditSpeaker`, `ViewSpeaker`
- **Custom pages:** `SpeakerPortalPage` (speaker self-submission), `AgendaBuilderPage`
- **Widgets:** `ConfirmedSpeakersWidget`, `ContractStatusWidget`
- **Nav group:** Content

---

## Displaces

| Feature | FlowFlex | Cvent | Sessionize | Eventbrite |
|---|---|---|---|---|
| Speaker profiles | Yes | Yes | Yes | No |
| Session assignment | Yes | Yes | Yes | No |
| Travel and accommodation | Yes | Yes | No | No |
| AI bio editing | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[events]] — speakers assigned to events
- [[post-event-analytics]] — speaker feedback scores included in analytics
