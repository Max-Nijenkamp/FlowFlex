---
type: module
domain: HR & People
panel: hr
module-key: hr.feedback
status: planned
color: "#4ADE80"
---

# Employee Feedback

> Continuous feedback between employees and managers — praise, development suggestions, and peer recognition outside of formal review cycles.

**Panel:** `hr`
**Module key:** `hr.feedback`

## What It Does

Employee Feedback enables a culture of continuous, real-time feedback that is not tied to the annual or quarterly review cycle. Employees and managers can send structured feedback to any colleague at any time — praising a specific behaviour, suggesting a development area, or recognising a team contribution. Feedback is visible to both the giver and receiver. HR has aggregate visibility — which employees are giving and receiving feedback — but does not see individual feedback content unless the receiver explicitly shares it. Feedback history surfaces in performance reviews as supporting evidence.

## Features

### Core
- Feedback types: `praise`, `development`, `recognition`, `general`
- Sender and recipient: any employee can send feedback to any other employee — not restricted to manager/direct-report pairs
- Feedback form: type, competency tag (optional), written message (required, min 50 characters)
- Feedback inbox: received feedback shown in the employee's Self-Service portal — sorted by date
- Sent feedback history: sender can see all feedback they have given

### Advanced
- Competency tags: link feedback to specific competency areas defined in the company's performance framework — allows aggregation by competency in reviews
- Requested feedback: employee requests feedback from specific colleagues — generates a prompt sent via notification
- Manager visibility: managers can see all feedback (given and received) for their direct reports — not cross-team
- Anonymised sending option: sender can mark feedback anonymous — recipient sees the feedback text but not the sender name; HR can see the sender for escalation purposes
- Aggregated feedback summary: in Performance Reviews, the review form includes a digest of feedback received since the last cycle as supporting context

### AI-Powered
- Tone analysis: before submission, AI analyses the tone of written feedback and flags potentially harsh or unconstructive wording — suggests a more constructive rephrasing (does not block submission)
- Theme extraction: aggregate common competency themes mentioned in feedback across the company — surfaced in HR Analytics as a company-wide strengths and development areas view

## Data Model

```erDiagram
    employee_feedback {
        ulid id PK
        ulid company_id FK
        ulid sender_id FK
        ulid recipient_id FK
        string type
        string competency_tag
        text message
        boolean is_anonymous
        boolean is_requested
        ulid request_id FK
        timestamps created_at/updated_at
    }

    feedback_requests {
        ulid id PK
        ulid company_id FK
        ulid requester_id FK
        ulid requested_from_id FK
        string context
        string status
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `is_anonymous` | Hides sender_id from recipient view (HR can still see) |
| `competency_tag` | Optional — links to performance framework competency |
| `feedback_requests.status` | pending / completed / declined |

## Permissions

- `hr.feedback.send`
- `hr.feedback.view-own`
- `hr.feedback.view-team`
- `hr.feedback.request-feedback`
- `hr.feedback.view-aggregate-themes`

## Filament

- **Resource:** None (feedback is surfaced in Self-Service and Review pages)
- **Pages:** `FeedbackInboxPage` — employee's received feedback list
- **Custom pages:** `FeedbackInboxPage`, `GiveFeedbackPage`
- **Widgets:** `RecentFeedbackWidget` — latest received feedback snippet on Self-Service dashboard
- **Nav group:** Employees (hr panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Lattice | Continuous feedback between employees |
| 15Five | Weekly check-ins and feedback |
| Leapsome | Continuous feedback and recognition |
| Reflektive | Real-time feedback and recognition |

## Related

- [[performance-reviews]]
- [[employee-self-service]]
- [[employee-wellbeing]]
- [[talent-intelligence]]
