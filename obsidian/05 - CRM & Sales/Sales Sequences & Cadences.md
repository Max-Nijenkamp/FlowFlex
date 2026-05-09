---
tags: [flowflex, domain/crm, sequences, cadences, outbound, phase/3]
domain: CRM & Sales
panel: crm
color: "#2563EB"
status: planned
last_updated: 2026-05-08
---

# Sales Sequences & Cadences

Automated multi-step outreach for prospecting and follow-up. Email + call + LinkedIn tasks in a structured sequence — run automatically, feel personal. A rep sets up the cadence once; the platform handles timing and logging. Replaces Salesloft, Outreach, and Apollo.io sequences without the €400/mo price tag.

**Who uses it:** Sales reps, BDRs/SDRs, account managers
**Filament Panel:** `crm`
**Depends on:** Core, [[Contact & Company Management]], [[Sales Pipeline]], [[Email Marketing]] (sending infra), [[AI Content Studio]]
**Phase:** 3

---

## Features

### Sequence Builder

- Create named sequences: "Cold Outbound SaaS", "Demo No-Show Follow-up", "Trial Expiry Nurture"
- Steps:
  - **Email** — automated send from rep's connected inbox (Google/Outlook via OAuth)
  - **Call task** — reminder task for rep to call at a specific time
  - **LinkedIn task** — reminder to connect/message on LinkedIn
  - **Manual task** — free-form reminder: "send case study", "check in via WhatsApp"
  - **Wait** — delay before next step (hours, business days)
- AI step: optional AI-generated email draft (personalised from contact data)
- Branching: if email opened → skip next email, go to call step
- Finishing conditions: reply received, meeting booked, deal created → exit sequence automatically

### AI Personalisation

- First-line personalisation: AI generates unique opening line per contact using: job title, company, recent news, mutual connections, LinkedIn headline
- Personalisation fields: {{first_name}}, {{company}}, {{industry}}, {{pain_point}}, {{custom_field}}
- AI rewrite: one-click rewrite of any step in different tone (formal/casual/direct)
- Review before send: rep can review and tweak AI-generated content before sequence goes live

### Enrolling Contacts

- Enrol individually from contact record
- Bulk enrol from list/segment (max 500/day per sender domain to protect deliverability)
- Enrolment conditions: automatically enrol when stage changes to "New Lead" or form submitted
- Priority: urgent contacts moved to top of sending queue

### Inbox Connection

- Connect Google Workspace or Outlook via OAuth (OAuth tokens stored encrypted)
- Emails sent from rep's actual inbox — reply-to is the rep's address
- Replies tracked: auto-detect replies → pause sequence, create CRM activity
- Bounces + OOO: auto-detect, pause sequence, flag for review
- Send schedule: only send emails during business hours (configurable timezone)

### Call & Task Management

- Call tasks appear in rep's task queue for the scheduled day
- Click-to-call: integrated with Twilio if enabled
- Call log: rep logs outcome (connected/voicemail/no-answer) + notes, time logged automatically
- Task snooze: postpone a step to next day

### Analytics

- Sequence performance: open rate, reply rate, click rate, meeting booked rate, deal created rate per step
- Rep performance: contacts enrolled, emails sent, calls logged, pipeline generated
- A/B test: test two email variants on the same step, winner auto-selected after statistical significance
- Best time to send: AI analyses per-contact open history to schedule at optimal time

### Deliverability

- Daily send limits per connected inbox (configurable, default 100/day)
- Domain warmup tracker: ramp from 10→100/day over 30 days for new domains
- Unsubscribe link: auto-appended to all sequence emails (GDPR/CAN-SPAM)
- Suppression: contacts marked "do not contact" are never enrolled

---

## Database Tables (4)

### `crm_sequences`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `description` | text nullable | |
| `steps` | json | [{step_type, delay_business_days, template_id, task_description}] |
| `finishing_conditions` | json | [{trigger, action: exit}] |
| `is_active` | boolean | |
| `created_by` | ulid FK | |

### `crm_sequence_enrolments`
| Column | Type | Notes |
|---|---|---|
| `sequence_id` | ulid FK | |
| `contact_id` | ulid FK | |
| `enrolled_by` | ulid FK | |
| `current_step_index` | integer default 0 | |
| `status` | enum | `active`, `paused`, `completed`, `replied`, `bounced`, `opted_out` |
| `enrolled_at` | timestamp | |
| `finished_at` | timestamp nullable | |
| `finish_reason` | string nullable | |

### `crm_sequence_step_logs`
| Column | Type | Notes |
|---|---|---|
| `enrolment_id` | ulid FK | |
| `step_index` | integer | |
| `step_type` | enum | `email`, `call`, `linkedin`, `task` |
| `executed_at` | timestamp nullable | |
| `opened_at` | timestamp nullable | |
| `replied_at` | timestamp nullable | |
| `clicked_at` | timestamp nullable | |
| `outcome` | string nullable | call: connected/vm/no-answer |

### `crm_connected_inboxes`
| Column | Type | Notes |
|---|---|---|
| `owner_id` | ulid FK | rep |
| `provider` | enum | `google`, `outlook` |
| `email` | string | |
| `access_token_enc` | text | encrypted |
| `refresh_token_enc` | text | encrypted |
| `daily_send_limit` | integer default 100 | |
| `warmup_enabled` | boolean default false | |

---

## Permissions

```
crm.sequences.view
crm.sequences.create
crm.sequences.enrol
crm.sequences.view-analytics
crm.sequences.manage-inboxes
```

---

## Competitor Comparison

| Feature | FlowFlex | Salesloft | Outreach | Apollo.io |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€75+/user/mo) | ❌ (€100+/user/mo) | ❌ (€49+/mo) |
| AI personalised first line | ✅ | ✅ | ✅ | ✅ |
| Integrated with CRM | ✅ (native) | ✅ (Salesforce) | ✅ (Salesforce) | partial |
| Email + call + LinkedIn steps | ✅ | ✅ | ✅ | ✅ |
| A/B test steps | ✅ | ✅ | ✅ | ✅ |
| Deliverability warmup | ✅ | ❌ | ❌ | ✅ |

---

## Related

- [[CRM Overview]]
- [[Contact & Company Management]]
- [[Sales Pipeline]]
- [[AI Content Studio]]
- [[Email Marketing]]
- [[AI Sales Coach]]
