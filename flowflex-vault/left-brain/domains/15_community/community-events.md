---
type: module
domain: Community & Social
panel: community
phase: 7
status: complete
cssclasses: domain-community
migration_range: 801000–801499
last_updated: 2026-05-12
right_brain_log: "[[builder-log-community-phase7]]"
---

# Community Events

Schedule and run community-specific events: virtual meetups, AMAs (Ask Me Anything), webinars, office hours. Built for community members — simpler than the full [[Events]] domain.

---

## Event Types

| Type | Format |
|---|---|
| AMA | Live Q&A with guest expert or founder |
| Office hours | Open session for community questions |
| Meetup | Regional in-person or virtual networking |
| Workshop | Hands-on learning session |
| Hackathon | Build challenge, prizes |
| Beta launch | New feature preview for community |

---

## Community Event vs Full Event

Community events use the full [[Events]] domain (registration, ticketing, check-in) but are surfaced inside the community with:
- Prominent placement on community homepage
- RSVP without leaving community
- Post-event discussion thread auto-created
- Recording + notes published to community after event
- Member event badges: "Attended Summit 2026"

---

## RSVP

Members RSVP from community event page:
- Add to calendar (iCal / Google)
- Reminder notification (T-1 day, T-1 hour)
- Waitlist if capacity hit
- "Bring a friend" sharing link

---

## AMAs

Structured AMA format:
1. Pre-event: members submit questions in thread
2. Community upvotes questions (best questions rise to top)
3. Live: host answers top questions + takes live questions
4. Post: full recording + transcript + top questions/answers published

---

## Data Model

Uses [[Events]] domain tables for the event record. Community-specific additions:

### `comm_community_events`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| community_id | ulid | FK |
| event_id | ulid | FK → evt_events |
| thread_id | ulid | nullable FK → comm_threads |
| type | varchar(50) | ama/office_hours/meetup/etc |
| host_member_id | ulid | nullable FK |

---

## Migration

```
801000_create_comm_community_events_table
```

---

## Related

- [[MOC_Community]]
- [[discussion-forums]]
- [[MOC_Events]] — event infrastructure
- [[gamification-points]]
