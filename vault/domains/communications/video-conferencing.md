---
type: module
domain: Communications
panel: comms
module-key: comms.video
status: planned
color: "#4ADE80"
---

# Video Conferencing

> Schedule meetings with auto-generated video links, sync with employee calendars, and store recordings within FlowFlex.

**Panel:** `comms`
**Module key:** `comms.video`

## What It Does

Video Conferencing handles meeting scheduling and management inside FlowFlex. When a meeting is created, a video link is automatically generated — either through a native embedded video provider integration (Daily.co, Whereby) or by generating a Zoom/Teams link via their respective APIs. The meeting appears in the invitees' FlowFlex calendars with a one-click join button. Recordings from completed meetings can be stored, linked to the meeting record, and accessed by participants afterwards.

## Features

### Core
- Meeting creation: title, description, start time, end time, organiser, and invitees (FlowFlex users or external email addresses)
- Video link generation: auto-generate a meeting link via integrated provider (Daily.co for native, or Zoom/Teams/Google Meet via API)
- Calendar integration: meeting appears in FlowFlex calendar; invite sent to external invitees via email; iCal attachment included
- One-click join: join button on the meeting record and in the invite email; no separate login required for native provider
- Meeting status: upcoming, in progress, completed, cancelled
- Recurring meetings: configure weekly, bi-weekly, or monthly recurring series with individual occurrence management

### Advanced
- Meeting agenda: structured agenda builder; agenda items with owner and time allocation; agenda included in invite email
- Recording storage: after the meeting, the recording URL is attached to the meeting record; accessible to all invitees; stored in FlowFlex file storage
- Recording transcript: AI-generated transcript attached to the meeting record for search and reference
- Meeting notes: collaborative note-taking area on the meeting record; updates visible to all invitees during and after the call
- Action items: create tasks directly from meeting notes; assigned to attendees with due dates; sync to [[../projects/INDEX]] task management
- Room booking: link a physical meeting room (from [[../hr/INDEX]] room resource calendar) to a video meeting record

### AI-Powered
- Meeting summary: AI-generated bullet-point summary of key discussion points and decisions from the transcript
- Action item extraction: automatically extract and create tasks from meeting transcript action points

## Data Model

```erDiagram
    comms_meetings {
        ulid id PK
        ulid company_id FK
        string title
        text description
        ulid organiser_id FK
        timestamp starts_at
        timestamp ends_at
        string video_provider
        string video_link
        string status
        string recording_url
        string transcript_url
        boolean is_recurring
        string recurrence_rule
        timestamps timestamps
    }

    comms_meeting_attendees {
        ulid id PK
        ulid meeting_id FK
        ulid user_id FK
        string external_email
        string rsvp_status
        boolean attended
    }

    comms_meeting_notes {
        ulid id PK
        ulid meeting_id FK
        ulid author_id FK
        text content
        timestamps timestamps
    }

    comms_meetings ||--o{ comms_meeting_attendees : "has"
    comms_meetings ||--o{ comms_meeting_notes : "has"
```

| Table | Purpose |
|---|---|
| `comms_meetings` | Meeting records with video link and recording |
| `comms_meeting_attendees` | Invitees with RSVP and attendance status |
| `comms_meeting_notes` | Collaborative meeting notes |

## Permissions

```
comms.video.view-any
comms.video.create
comms.video.update
comms.video.record
comms.video.delete
```

## Filament

**Resource class:** `MeetingResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `MeetingCalendarPage` (calendar view of all scheduled meetings)
**Widgets:** `UpcomingMeetingsWidget` (next 5 meetings for the current user)
**Nav group:** Broadcast

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Calendly + Zoom | Meeting scheduling with video link generation |
| Microsoft Teams Meetings | Integrated calendar and video meetings |
| Google Meet + Calendar | Meeting booking and video conferencing |
| Loom | Async video recording and storage |

## Implementation Notes

**External dependency — video provider (must be decided before build):** The spec lists multiple options. The choice determines the entire API integration path:

1. **Daily.co** — REST API for room creation; embedded via `<daily-co>` web component. Native in-browser video, no app install. Works inside an iFrame in the Filament panel. Recommended for native FlowFlex-embedded video.
2. **Whereby Embedded** — similar REST API + iFrame embed approach.
3. **Zoom API** — OAuth app, Zoom Meeting API to create meetings and get join URLs. Users need a Zoom account or join as a guest via browser. Recording retrieval via Zoom's recording API requires polling or a Zoom webhook.
4. **Google Meet / Microsoft Teams** — generate links via their respective calendar APIs (Google Calendar API / Microsoft Graph API). These do NOT embed — they open external windows.

**Decision required:** Choose one native embedded provider (Daily.co or Whereby) for the default FlowFlex experience, and optionally allow OAuth-connected external providers (Zoom, Teams) as alternatives. Store the provider choice per meeting in `comms_meetings.video_provider`. The service layer `app/Services/Comms/VideoProviderService.php` should be an interface with concrete implementations per provider.

**Calendar integration:** `MeetingCalendarPage` is a custom Filament `Page` using a month/week/day calendar widget. The calendar component must be built with a JavaScript calendar library — **FullCalendar.js** (MIT) is the recommended choice, rendered in a Blade partial within the Livewire component. Events are loaded via a Livewire `getEvents()` method returning JSON.

**Real-time:** Reverb is not required for meeting management itself. Meeting reminders are sent via the standard notifications system (queued `MeetingReminderNotification` job, dispatched 10 and 1 minute before start time).

**Recording storage:** Recordings from Daily.co/Whereby are stored as URLs in `comms_meetings.recording_url`. For providers that store recordings on their platform, only the URL is stored in FlowFlex. If the company wants recordings stored in FlowFlex file storage (S3/R2), a background job (`DownloadMeetingRecordingJob`) downloads the file from the provider and uploads it via spatie/laravel-media-library.

**AI features:** Meeting transcript and summary call OpenAI GPT-4o via `app/Services/AI/MeetingTranscriptService.php`. The transcript must first be retrieved from the video provider (Daily.co has a transcription API; Zoom provides transcripts via their API). Action item extraction uses a structured JSON prompt to return an array of `{assignee, action, due_date}` objects that are then converted into `proj_tasks` records.

**iCal generation:** Use `spatie/laravel-google-calendar` is NOT needed — generate iCal `.ics` files directly using `eluceo/ical` package or a simple string builder. Attach as a `calendar.ics` file to the invite email via Laravel Mail.

## Related

- [[messaging]] — start a video call from a direct message conversation
- [[team-channels]] — schedule meetings from within a channel
- [[announcements]] — all-hands recordings stored as meeting records
- [[notification-center]] — meeting reminders surface in the notification centre
