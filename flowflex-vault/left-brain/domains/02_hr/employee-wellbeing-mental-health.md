---
type: module
domain: HR & People
panel: hr
cssclasses: domain-hr
phase: 8
status: complete
migration_range: 100000–149999
last_updated: 2026-05-12
---

# Employee Wellbeing & Mental Health

Wellbeing check-ins, burnout early warning, EAP (Employee Assistance Programme) resource hub, wellbeing challenges, and aggregate team health reporting. Replaces Unmind, Wellhub (Gympass), and Spill.

---

## Features

### Wellbeing Check-ins
- Weekly pulse: "How are you feeling?" (1–5 mood scale + optional note)
- Always anonymous at team/org level — individual data private unless employee shares
- Trend tracking over time per employee
- Manager dashboard: team mood aggregate (never individual unless shared)
- Automatic check-in nudge via email/notification

### Burnout Early Warning
- Algorithm: sustained low mood + high workload (leave days + overtime from Payroll) + no PTO taken → burnout risk flag
- Proactive nudge to employee: "You haven't taken time off in 60 days"
- Manager alert: "3 people on your team are showing high burnout signals" (anonymised)
- Escalation path to HR for intervention

### EAP Resource Hub
- Curated mental health resources (articles, guided meditations, breathing exercises)
- Employee self-service: request EAP counselling sessions (confidential)
- Partner integrations: Spill, Health Assured, Cigna EAP
- GDPR-compliant: HR/manager sees only aggregate stats, not individual therapy usage

### Wellbeing Challenges
- Company-wide challenges: step count, mindfulness, hydration, reading
- Team vs team leaderboard (opt-in)
- Integration with wearables (Google Fit, Apple Health)
- Points redeemable via Benefits & Perks module

### Benefits Integration
- Gym membership management (Gympass/Wellhub integration or manual)
- Therapy session allowance tracking
- Mental health days (special leave type in Leave Management)
- Wellbeing budget per employee

### Reporting
- Aggregate wellbeing score (company, department, team)
- eNPS correlation with wellbeing (from Employee Feedback module)
- Absence vs wellbeing correlation
- ROI on wellbeing spend

---

## Data Model

```erDiagram
    wellbeing_checkins {
        ulid id PK
        ulid company_id FK
        ulid employee_id FK
        integer mood_score
        text note
        boolean shared_with_manager
        date checkin_date
    }

    eap_resources {
        ulid id PK
        ulid company_id FK
        string title
        string category
        string url
        boolean is_pinned
    }

    wellbeing_challenges {
        ulid id PK
        ulid company_id FK
        string name
        string type
        date starts_at
        date ends_at
        boolean is_active
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `BurnoutSignalDetected` | Algorithm flags risk | Notifications (employee + anonymised manager), HR |
| `WellbeingChallengeCompleted` | Employee completes | HR (award points), Notifications |

---

## Permissions

```
hr.wellbeing.view-own
hr.wellbeing.view-team-aggregate
hr.wellbeing.manage-resources
hr.wellbeing.manage-challenges
```

---

## Competitors Displaced

Unmind · Wellhub (Gympass) · Spill · Leapsome (wellbeing) · Peakon (now Workday Peakon)

---

## Related

- [[MOC_HR]]
- [[entity-employee]]
- [[leave-management]] — mental health days as leave type
- [[employee-feedback]] — eNPS correlation
