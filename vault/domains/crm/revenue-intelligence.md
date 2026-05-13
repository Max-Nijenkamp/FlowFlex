---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.revenue-intelligence
status: planned
color: "#4ADE80"
---

# Revenue Intelligence

> AI-powered deal health scoring, at-risk deal identification, win/loss analysis, and competitive intelligence — a read-only analytics layer on top of all CRM data.

**Panel:** `crm`
**Module key:** `crm.revenue-intelligence`

## What It Does

Revenue Intelligence is a read-only analytics and AI layer that continuously analyses all CRM data — deal activity, email engagement, buyer sentiment, deal room interactions, contract signals, and historical win/loss patterns — to surface actionable insights for sales leadership. It does not create or modify records; it synthesises signals from across the CRM domain and presents them as deal health scores, at-risk alerts, and win/loss reports. The goal is to give sales managers early warning of deals heading toward loss and identify the patterns that correlate with wins.

## Features

### Core
- Deal health score: 0–100 score per open deal computed from activity recency, email engagement, deal age vs average close time, buyer sentiment from email analysis, and deal room engagement
- At-risk deals list: deals with health score below configurable threshold — sorted by value at risk — with the top signal driving the risk flag
- Win/loss analysis: analysis of closed deals — win rate by stage, by rep, by deal size, by industry, by source — filterable by period
- Rep performance: side-by-side comparison of deal health scores, pipeline coverage, and quota attainment per rep
- Pipeline velocity: average time spent per stage across all deals — identifies stages where deals slow down

### Advanced
- Competitive analysis: track which competitor names appear in notes and email content — frequency and correlation with won vs lost deals
- Deal risk alerts: when a deal's health score drops more than 20 points in a week, the deal owner and manager are notified via notification module
- Historical deal similarity: for any open deal, show the five most similar historical deals and whether they were won or lost — with key differentiating factors
- Revenue waterfall: start of period pipeline value → new deals added → deals won → deals lost → end of period remaining pipeline — variance from prior period
- Churn risk from contracts: integrate contract renewal signals into deal health scoring for renewal/upsell opportunities

### AI-Powered
- Natural language insights: AI generates a daily "Revenue Briefing" — three bullet points summarising the biggest changes in the pipeline since yesterday, written in plain English for the sales manager
- Next deal to focus on: AI recommends the single deal the rep should prioritise today based on a combination of close date, health score, and gap to quota

## Data Model

```erDiagram
    deal_health_scores {
        ulid id PK
        ulid deal_id FK
        ulid company_id FK
        integer score
        json signal_breakdown
        timestamp calculated_at
        timestamps created_at/updated_at
    }

    win_loss_analysis {
        ulid id PK
        ulid deal_id FK
        ulid company_id FK
        string outcome
        json analysis_data
        timestamp analysed_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `score` | 0–100; recomputed daily or on significant activity |
| `signal_breakdown` | JSON map of {signal: weight, value} for transparency |
| `outcome` | won / lost |
| `analysis_data` | Extracted insights from deal — competitor mentions, deal stage durations |

## Permissions

- `crm.revenue-intelligence.view`
- `crm.revenue-intelligence.view-rep-performance`
- `crm.revenue-intelligence.configure-thresholds`
- `crm.revenue-intelligence.export`
- `crm.revenue-intelligence.view-competitive`

## Filament

- **Resource:** None
- **Pages:** None
- **Custom pages:** `RevIntelDashboardPage` — tabbed: Deal Health, At-Risk Deals, Win/Loss Analysis, Rep Performance, Pipeline Velocity
- **Widgets:** `AtRiskDealsWidget`, `DealHealthDistributionWidget`
- **Nav group:** Intelligence (crm panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Gong | Revenue intelligence and conversation analysis |
| Clari | AI-powered pipeline management |
| Chorus.ai | Deal intelligence and coaching |
| People.ai | Revenue intelligence platform |

## Related

- [[deals]]
- [[activities]]
- [[email-integration]]
- [[deal-rooms]]
- [[forecasting]]
- [[contracts]]
