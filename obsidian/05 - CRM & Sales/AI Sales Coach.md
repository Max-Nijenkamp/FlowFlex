---
tags: [flowflex, domain/crm, ai-sales-coach, revenue-intelligence, phase/6]
domain: CRM & Sales
panel: crm
color: "#2563EB"
status: planned
last_updated: 2026-05-08
---

# AI Sales Coach

Real-time coaching for your sales team. Analyses deal activity, surfaces risk early, suggests next best actions, and helps reps improve through data — not gut feel.

**Who uses it:** Sales reps, sales managers, revenue operations
**Filament Panel:** `crm`
**Depends on:** [[Sales Pipeline]], [[Contact & Company Management]], [[AI Infrastructure]]
**Phase:** 6

---

## Features

### Deal Health Scoring

Every deal in the pipeline gets a health score (0–100):

Factors that increase score:
- Recent meaningful activity (meeting, email reply, demo scheduled)
- Multiple stakeholders engaged on buyer side
- Clear next step with date set
- Champion identified
- Positive sentiment in communications

Factors that decrease score:
- No activity in 7+ days
- Close date in the past without update
- Only one contact engaged
- No next step set
- Competitor mentioned
- Budget not confirmed

Score displayed on deal card and in list view. Colour: green (70+), amber (40–69), red (<40).

### Pipeline Risk Report

Weekly automated report to sales managers:
- Deals at risk (health score dropped >20 points)
- Stalled deals (no activity > N days — configurable)
- Close date slippage (moved right more than once)
- Missing next steps
- Overall pipeline health score vs last week

### Next Best Action Suggestions

Per deal, AI suggests what to do next:
- "Send a case study — Acme Corp mentioned ROI concerns in last meeting"
- "Schedule executive alignment call — only working with individual contributor"
- "Send pricing — they asked for it 5 days ago and haven't received a response"
- "Revisit this deal — it's been cold for 21 days"

Suggestions shown on deal detail page, dismissible with "Done" or "Not relevant".

### Email Intelligence

- Write AI-suggested email responses from deal page
- Context-aware: knows the deal stage, recent activity, company info
- Tone: professional, concise, helpful (not generic)
- One-click send or edit before sending
- Email sentiment tracking: detect negative/frustrated responses, flag to manager

### Win/Loss Analysis

- After deal is marked Won or Lost, prompt for reason
- AI analyses patterns: what do won deals have in common vs lost?
- Win rate by: industry, deal size, rep, source, sales cycle length
- Loss reason clustering: groups similar reasons, shows top 5 loss drivers
- Competitive win rate: when X competitor mentioned, what's our win rate?

### Rep Performance Coaching

- Individual rep dashboard: activity vs target, close rate, avg deal size, sales cycle
- Trend chart: improving or declining over time?
- Comparison: rep performance vs team median (anonymous benchmarking)
- Manager coaching notes: private notes attached to rep profile
- Suggested focus areas for 1:1 based on weakest metrics

---

## Database Tables (3)

### `crm_deal_health_scores`
| Column | Type | Notes |
|---|---|---|
| `deal_id` | ulid FK | |
| `score` | integer | 0-100 |
| `score_factors` | json | breakdown of contributing factors |
| `calculated_at` | timestamp | |

### `crm_next_actions`
| Column | Type | Notes |
|---|---|---|
| `deal_id` | ulid FK | |
| `action_text` | text | AI suggestion |
| `action_type` | string | e.g. `send_email`, `schedule_call` |
| `priority` | enum | `urgent`, `recommended`, `optional` |
| `dismissed_at` | timestamp nullable | |
| `completed_at` | timestamp nullable | |
| `generated_at` | timestamp | |

### `crm_win_loss_reasons`
| Column | Type | Notes |
|---|---|---|
| `deal_id` | ulid FK | |
| `outcome` | enum | `won`, `lost`, `no_decision` |
| `reason` | text | |
| `competitor_mentioned` | string nullable | |
| `ai_category` | string nullable | AI-clustered reason category |

---

## Permissions

```
crm.ai-coach.view-own
crm.ai-coach.view-team
crm.ai-coach.view-company
crm.win-loss.view
crm.win-loss.edit
```

---

## Competitor Comparison

| Feature | FlowFlex | Gong.io | Chorus (ZoomInfo) | HubSpot Sales Hub |
|---|---|---|---|---|
| Deal health scoring | ✅ | ✅ | ✅ | ✅ |
| Next best action AI | ✅ | ✅ | partial | ✅ |
| Win/loss analysis | ✅ | ✅ | ✅ | partial |
| Call recording/analysis | planned | ✅ (core feature) | ✅ (core feature) | partial |
| Included in base platform | ✅ | ❌ (€100+/user/mo) | ❌ (€80+/user/mo) | ❌ (Sales Hub Pro) |

---

## Related

- [[CRM Overview]]
- [[Sales Pipeline]]
- [[Revenue Intelligence & Forecasting]]
- [[AI Infrastructure]]
