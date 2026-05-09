---
type: moc
domain: Customer Success
panel: cs
phase: 5
color: "#0EA5E9"
cssclasses: domain-cs
last_updated: 2026-05-09
---

# Customer Success — Map of Content

Post-sale retention platform for B2B SaaS and services companies. Health scoring, playbooks, QBR preparation, onboarding tracking, renewal forecasting, and expansion revenue. Replaces Gainsight, ChurnZero, Vitally, Catalyst, and Planhat.

**Panel:** `cs`  
**Phase:** 5  
**Migration Range:** `970000–974999`  
**Colour:** Sky `#0EA5E9` / Light: `#E0F2FE`  
**Icon:** `heroicon-o-heart`

---

## Why This Domain Exists

Customer Success is the highest-ROI function in B2B SaaS — cheaper to retain than acquire. Yet the tooling is brutally expensive:
- Gainsight: €150k+/year (enterprise only)
- ChurnZero: €15k+/year
- Vitally / Catalyst / Planhat: €12k–40k/year

Every B2B SaaS company using FlowFlex to run their business also needs CS tooling. By including it, FlowFlex becomes the single platform for the entire customer lifecycle: marketing → CRM → revenue → CS → renewal.

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| Customer Health Scoring | 5 | planned | Composite health score from product usage, support tickets, NPS, billing |
| CS Playbooks & Alerts | 5 | planned | Triggered playbook tasks when health drops, milestone hit, or renewal due |
| QBR Preparation | 5 | planned | Auto-populate QBR deck with usage data, metrics, achievements |
| Customer Onboarding Tracking | 5 | planned | Structured onboarding milestone plan per customer |
| Renewal Forecasting | 5 | planned | Renewal pipeline, risk flags, renewal probability scoring |
| Expansion Revenue Tracking | 6 | planned | Upsell/cross-sell opportunities, expansion ARR analytics |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `HealthScoreDropped` | Health Scoring | CS (trigger playbook), CRM (update contact), Notifications |
| `RenewalDueSoon` | Renewal Forecasting | CS (create renewal task), CRM (alert AE), Finance |
| `OnboardingMilestoneCompleted` | Onboarding | Notifications (CSM + customer), PLG (track activation) |
| `ExpansionOpportunityIdentified` | Expansion | CRM (create opportunity), Notifications (CSM) |

---

## Filament Panel Structure

**Navigation Groups:**
- `Portfolio` — All Accounts, Health Dashboard, At-Risk Accounts
- `Playbooks` — Active Plays, Playbook Library, Alerts Inbox
- `Onboarding` — Onboarding Plans, Milestones, Templates
- `Renewals` — Renewal Pipeline, Forecasts, Renewal History
- `Expansion` — Upsell Opportunities, Expansion Pipeline

---

## Relationship to CRM

CS extends CRM — it operates on the same Company and Contact records but adds CS-specific layers:
- CSM assignment (separate from AE ownership in CRM)
- Health score (CS-owned metric)
- CS activities (QBRs, check-ins) logged separately from sales activities
- Renewal stage (CS-managed) vs opportunity stage (Sales-managed)

---

## Permissions Prefix

`cs.accounts.*` · `cs.playbooks.*` · `cs.onboarding.*`  
`cs.renewals.*` · `cs.expansion.*`

---

## Competitors Displaced

Gainsight · ChurnZero · Totango · Vitally · Catalyst · Planhat · Gainsight PX · ClientSuccess

---

## Related

- [[MOC_Domains]]
- [[MOC_CRM]] — same company/contact records
- [[MOC_Finance]] — renewal ARR → revenue
- [[MOC_PLG]] — product usage data feeds health score
