---
type: module
domain: Product-Led Growth
panel: plg
cssclasses: domain-plg
phase: 7
status: planned
migration_range: 893000–894999
last_updated: 2026-05-09
---

# In-App Tours & Onboarding

No-code overlay tours, onboarding checklists, hotspots, and contextual tooltips. Guides users to activation without engineering involvement. Replaces Appcues and Pendo.

---

## Element Types

### Tour Steps (Overlay)
- Tooltip attached to a CSS selector (element highlight + popover)
- Modal step (full-screen takeover for critical moments)
- Video step (embedded tutorial video)
- Hotspot (pulsing beacon on a UI element, click to expand tooltip)

Each step has:
- Target selector (CSS or XPath)
- Title + body copy (rich text)
- CTA button (next / skip / go to URL / trigger action)
- Position: top / bottom / left / right / auto

### Checklist
- Floating checklist widget (collapsible, pinned to corner)
- Tasks with completion triggers:
  - URL visit (user navigated to `/settings`)
  - Event fired (user triggered `profile_completed` event)
  - Manual dismissal
- Progress bar shows X/Y tasks complete
- Completion reward: badge, confetti, unlock next feature

### Announcement Banner
- Top-of-screen dismissible banner
- Used for: new feature announcement, maintenance warning, trial expiry

---

## Targeting & Triggers

**Show tour to:**
- New users (first login, account created < 7 days ago)
- Users who haven't completed a specific event (e.g., never connected integration)
- Users in a specific segment (from [[user-segmentation]])
- Users with feature flag enabled (from [[feature-flags]])

**Trigger on:**
- Page load (URL match with regex/glob)
- Element visibility (show when specific element appears in DOM)
- Time delay (e.g., show after 5 seconds on page)
- Custom event (fire from your app: `FlowFlex.tours.trigger('connect_integration_tour')`)
- Manual trigger from admin (push tour to specific user for support purposes)

**Frequency:**
- Show once / show until dismissed / show every session / show every X days

---

## No-Code Builder

Visual editor in FlowFlex admin:
1. Enter URL of your product (opens in iframe)
2. Click elements to attach steps
3. Edit copy, position, CTA inline
4. Set targeting rules
5. Publish or save as draft

Changes are live immediately (loaded via JS SDK, no deploy needed).

---

## Data Model

### `plg_flows`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(200) | |
| type | enum | tour/checklist/banner/hotspot |
| status | enum | draft/active/paused/archived |
| targeting_rules | json | |
| frequency | enum | once/until_dismissed/every_session/interval |
| frequency_days | int | nullable |

### `plg_flow_steps`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| flow_id | ulid | FK |
| step_order | int | |
| type | enum | tooltip/modal/video/hotspot |
| target_selector | varchar | nullable |
| title | varchar(300) | |
| body | text | |
| cta_label | varchar(100) | |
| cta_action | json | {type: "next"|"url"|"event", value: "..."} |

### `plg_flow_completions`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| flow_id | ulid | FK |
| user_id | varchar | end-user identifier |
| started_at | timestamp | |
| completed_at | timestamp | nullable |
| abandoned_at | timestamp | nullable |
| last_step | int | nullable |

---

## Analytics

Per flow:
- View rate (users who saw step 1)
- Completion rate (users who finished all steps)
- Drop-off per step (where users abandon)
- Time to complete
- Impact: conversion rate of users who completed tour vs who didn't

---

## Migration

```
893000_create_plg_flows_table
893001_create_plg_flow_steps_table
893002_create_plg_flow_completions_table
893003_create_plg_flow_step_views_table
```

---

## Related

- [[MOC_PLG]]
- [[feature-flags]] — flag-gated tours
- [[user-segmentation]] — target by segment
- [[product-usage-analytics]] — measure tour impact
- [[in-app-nps-feedback]] — post-tour NPS trigger
