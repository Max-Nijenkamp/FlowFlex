---
type: module
domain: Events Management
panel: events
phase: 5
status: planned
cssclasses: domain-events
migration_range: 992500–992999
last_updated: 2026-05-09
---

# Post-Event Analytics

Measure event success. Attendance vs registration, session popularity, net promoter score, ROI calculation, and lead generation tracking.

---

## Core Metrics

| Metric | Definition |
|---|---|
| Registration count | Total tickets registered |
| Attendance rate | Checked-in / registered % |
| No-show rate | 1 − attendance rate |
| Session fill rate | Checked-in per session / session capacity |
| Revenue | Total ticket sales − refunds |
| Cost per attendee | Total event cost / attendees |

---

## Attendance Analysis

- Arrival curve: check-ins over time (when did people arrive?)
- Session-by-session attendance (which sessions were popular?)
- Drop-off analysis: attendees who left early
- Virtual vs in-person split (hybrid events)

---

## NPS / Satisfaction Survey

Auto-sent post-event (configurable delay: e.g., 2 hours after event ends):
- NPS question: "How likely are you to recommend this event? 0–10"
- 3–5 additional questions: session quality, venue, content, speakers
- Optional open text

Results:
- NPS score calculated (Promoters − Detractors)
- Per-session ratings
- Word cloud from open text responses
- Comparison vs previous editions

---

## Lead Generation Report

For events with sales purpose:
- New contacts generated (attendees not in CRM before)
- CRM contacts who attended (touchpoint tracking)
- Leads by company / industry / ticket type
- Follow-up tasks auto-created in CRM for sales team

---

## ROI Calculation

Event expenses tracked (venue, catering, production, marketing, staff):
- Revenue from tickets
- Pipeline influenced (CRM opportunities touched by attendees)
- Cost per lead / cost per opportunity

---

## Data Model

### `evt_survey_responses`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| event_id | ulid | FK |
| attendee_id | ulid | FK |
| nps_score | tinyint | nullable |
| responses | json | question → answer |
| submitted_at | timestamp | |

### `evt_event_expenses`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| event_id | ulid | FK |
| category | varchar(100) | venue/catering/production/marketing |
| amount | decimal(12,2) | |
| currency | char(3) | |
| description | varchar(300) | |

---

## Migration

```
992500_create_evt_survey_responses_table
992501_create_evt_event_expenses_table
```

---

## Related

- [[MOC_Events]]
- [[event-checkin-app]]
- [[attendee-management]]
- [[registration-ticketing]]
- [[MOC_CRM]] — lead attribution
