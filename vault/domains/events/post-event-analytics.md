---
type: module
domain: Events Management
panel: events
module-key: events.analytics
status: planned
color: "#4ADE80"
---

# Post-Event Analytics

> Read-only post-event metrics — attendance rate, feedback scores, revenue, NPS, and comparative event performance.

**Panel:** `events`
**Module key:** `events.analytics`

---

## What It Does

Post-Event Analytics aggregates all data from a completed event into a comprehensive performance report. It calculates attendance rate (registered vs checked-in), feedback NPS and session ratings from post-event surveys, total revenue from ticket sales and sponsorship, and no-show rates. The module also enables cross-event comparison so teams can identify trends in attendance, revenue, and satisfaction over time. Reports can be exported as a PDF for stakeholder distribution.

---

## Features

### Core
- Attendance rate: registered vs actually checked-in, with no-show percentage
- Revenue summary: ticket sales revenue, sponsorship revenue, and total event P&L
- Post-event NPS: overall event satisfaction score from attendee survey responses
- Session ratings: per-session and per-speaker feedback scores
- Demographic breakdown: attendee breakdown by company type, job title, or registration source
- Export: event performance report as PDF or CSV

### Advanced
- Comparative view: compare current event against prior editions of the same event type
- Trend analysis: attendance rate and NPS trend across all events over a selected period
- Waitlist analysis: number of waitlisted attendees as a demand signal for future capacity planning
- Source tracking: which acquisition channels (email, social, partner) drove registrations
- Sponsorship ROI: impressions, attendee interactions, and value delivered per sponsor tier

### AI-Powered
- Performance summary: AI drafts a plain-language event performance summary from the structured data
- Actionable insights: suggest specific improvements for the next event based on low-scoring feedback areas
- Benchmark comparison: compare event metrics against industry averages for the event type and audience size

---

## Data Model

```erDiagram
    event_analytics_snapshots {
        ulid id PK
        ulid event_id FK
        ulid company_id FK
        integer total_registered
        integer total_attended
        decimal attendance_rate
        integer total_no_shows
        decimal ticket_revenue
        decimal sponsorship_revenue
        decimal nps_score
        decimal avg_session_rating
        json source_breakdown
        timestamps created_at_updated_at
    }

    post_event_survey_responses {
        ulid id PK
        ulid event_id FK
        ulid registration_id FK
        integer overall_nps
        json session_ratings
        text open_feedback
        timestamp responded_at
    }

    event_analytics_snapshots ||--o{ post_event_survey_responses : "aggregates"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `event_analytics_snapshots` | Event-level aggregates | `id`, `event_id`, `attendance_rate`, `ticket_revenue`, `sponsorship_revenue`, `nps_score` |
| `post_event_survey_responses` | Survey responses | `id`, `event_id`, `registration_id`, `overall_nps`, `session_ratings`, `open_feedback` |

---

## Permissions

```
events.analytics.view
events.analytics.view-all-events
events.analytics.export
events.analytics.view-revenue
events.analytics.send-survey
```

---

## Filament

- **Resource:** None (read-only, no CRUD)
- **Pages:** N/A
- **Custom pages:** `PostEventAnalyticsPage`, `EventComparisonPage`, `SurveyResponsePage`
- **Widgets:** `AttendanceRateWidget`, `EventNpsWidget`, `EventRevenueWidget`
- **Nav group:** Analytics

---

## Displaces

| Feature | FlowFlex | Cvent | Bizzabo | Eventbrite |
|---|---|---|---|---|
| Attendance rate analytics | Yes | Yes | Yes | Yes |
| NPS and feedback scores | Yes | Yes | Yes | No |
| Revenue summary | Yes | Yes | Yes | Yes |
| AI performance summary | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[events]] — analytics computed after event completion
- [[registrations]] — registration and check-in data source
- [[sponsors]] — sponsorship revenue included in event P&L
- [[speakers]] — speaker feedback scores in session ratings
