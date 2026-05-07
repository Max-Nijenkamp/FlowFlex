---
tags: [flowflex, domain/hr, feedback, engagement, enps, phase/8]
domain: HR & People
panel: hr
color: "#7C3AED"
status: planned
last_updated: 2026-05-06
---

# Employee Feedback & Engagement

Ongoing pulse on how employees are feeling. Goes beyond annual surveys — captures real-time sentiment and flags burnout risk before it becomes an attrition event.

**Who uses it:** HR team, leadership, managers
**Filament Panel:** `hr`
**Depends on:** [[Employee Profiles]]
**Phase:** 8
**Build complexity:** Medium — 2 resources, 2 pages, 4 tables

## Events Fired

- `BurnoutSignalDetected` → consumed by HR managers, Notifications (alerts direct manager and HR)

## Features

### Pulse Surveys

- Pulse survey builder (create short 3–5 question surveys)
- Scheduled pulse delivery (weekly, fortnightly, monthly)
- Random timing option (to prevent gaming the survey timing)
- eNPS question built-in: "How likely are you to recommend working here?" (scale 0–10)

### eNPS Tracking

- eNPS trend tracking over time
- Breakdowns by: department, tenure cohort, location
- Promoters / Passives / Detractors classification

### Anonymous Feedback

- Anonymous feedback toggle (employees answer without their name attached)
- HR team sees aggregate data, not individual responses when anonymous

### Sentiment Dashboard

- Aggregate scores and trend lines
- Department breakdowns
- Benchmark comparisons (against previous periods)

### Burnout Signal Detection

Algorithm monitors for composite signals:

| Signal | Description |
|---|---|
| Overtime frequency rising | More hours logged than contracted |
| Leave not being taken | Balance building up without use |
| Increasing after-hours activity | Login times / task activity outside hours |
| Declining pulse survey scores | Consecutive drops in sentiment scores |
| Increased sick leave frequency | More short-duration sick leave episodes |

When multiple signals coincide, manager and HR are alerted discreetly.

### Recognition & Kudos

- Peer-to-peer public recognition (anyone can give kudos to anyone)
- Manager-to-employee recognition
- Recognition feed (public wall of kudos visible to all)
- Birthday and work anniversary alerts for managers

## Database Tables (4)

1. `pulse_surveys` — survey definitions
2. `pulse_survey_responses` — responses per employee per survey (anonymised where set)
3. `burnout_signals` — logged signal events per employee
4. `recognitions` — kudos records

## Related

- [[HR Overview]]
- [[Employee Profiles]]
- [[Leave Management]]
- [[Time Tracking]]
