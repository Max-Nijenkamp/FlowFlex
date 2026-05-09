---
type: module
domain: Customer Success
panel: cs
phase: 5
status: planned
cssclasses: domain-cs
migration_range: 970700–970849
last_updated: 2026-05-09
---

# QBR Preparation

Auto-populate Quarterly Business Review decks with live customer data: usage metrics, value delivered, ROI, goals review, and roadmap preview. Cuts QBR prep from 4 hours to 30 minutes.

---

## QBR Deck Auto-Population

CSM selects customer + quarter → system generates QBR outline pre-filled with data:

### Slide / Section Templates

**1. Executive Summary**
- Customer health score trend (chart, last 4 quarters)
- Key wins this quarter (milestones achieved, auto-pulled from onboarding + CS activities)
- Items requiring attention (open risks, overdue items)

**2. Usage & Adoption**
- MAU (Monthly Active Users) last 3 months — from PLG
- Feature adoption matrix: which modules actively used, which untouched
- Power users identified (top 5 by event count)
- Usage vs licensed seats → adoption rate

**3. Support & Operational Health**
- Tickets raised this quarter: total, resolved, avg resolution time — from ITSM/helpdesk
- CSAT score (if tracked)
- Open issues (any pending)

**4. Value Delivered**
- KPIs the customer set at onboarding vs actuals (filled in by CSM)
- Quantified ROI (time saved, cost reduced — template with editable numbers)
- Notable outcomes / case study data (CSM-written, saved to account record)

**5. Goals Review**
- Goals set last QBR vs status
- New goals for next quarter (CSM input at meeting)

**6. Upcoming Roadmap**
- FlowFlex product roadmap highlights relevant to this customer's modules
- Renewal date reminder (if within 6 months)

---

## Output Formats

- **Google Slides**: OAuth push to customer's shared Google Drive folder
- **PowerPoint .pptx**: download
- **PDF**: branded, shareable
- **In-app view**: browser-based presentation (shareable link)

Branded with: FlowFlex logo + customer logo (uploaded to customer profile).

---

## Data Model

### `cs_qbr_sessions`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| crm_company_id | ulid | FK |
| csm_id | ulid | FK `employees` |
| quarter | varchar(7) | "2026-Q2" |
| scheduled_at | datetime | nullable |
| completed_at | datetime | nullable |
| deck_url | varchar | nullable |
| goals_set | json | array of {goal, metric, target} |
| notes | text | nullable |

---

## Migration

```
970700_create_cs_qbr_sessions_table
970701_create_cs_qbr_goals_table
```

---

## Related

- [[MOC_CustomerSuccess]]
- [[customer-health-scoring]] — health trend data
- [[customer-onboarding-tracking]] — achievements data
- [[renewal-forecasting]] — renewal context
- [[MOC_PLG]] — usage data source
