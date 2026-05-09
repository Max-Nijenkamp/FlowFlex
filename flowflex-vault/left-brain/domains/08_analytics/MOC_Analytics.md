---
type: moc
domain: Analytics & BI
panel: analytics
cssclasses: domain-analytics
phase: 6
color: "#0284C7"
last_updated: 2026-05-08
---

# Analytics & BI — Map of Content

Custom dashboards, report builder, KPI tracking, data warehouse export, audit logs, and AI-powered insights across all 15 domains.

**Panel:** `analytics`  
**Phase:** 6  
**Migration Range:** `450000–499999`  
**Colour:** Sky `#0284C7` / Light: `#E0F2FE`  
**Icon:** `heroicon-o-chart-bar`

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| [[dashboard-builder\|Dashboard Builder]] | 6 | planned | Drag-drop widgets, cross-domain metrics, sharing, scheduling |
| Report Builder | 6 | planned | No-code report builder, scheduled delivery, export |
| KPI & Goal Tracking | 6 | planned | KPI library, target vs actual, traffic lights |
| [[data-connectors-etl\|Data Connectors & ETL]] | 6 | planned | Pre-built connectors (Stripe, HubSpot, GA4, etc.), field mapping |
| Audit Log & Activity Trail | 6 | planned | Cross-domain user activity, tamper-evident logs |
| [[anomaly-detection-alerting\|Anomaly Detection & Alerting]] | 6 | planned | AI monitors KPIs, alerts on spikes/drops, threshold breaches |
| AI Insights Engine | 6 | planned | NL → SQL → chart, proactive anomaly detection |
| Predictive Analytics | 6 | planned | Deal win probability, churn risk, demand forecast |
| [[scheduled-reports\|Scheduled Reports]] | 6 | planned | Auto-email PDF/Excel/CSV reports on schedule to any recipient |
| [[embedded-analytics\|Embedded Analytics]] | 6 | planned | White-label dashboards embedded in portals, row-level security |

---

## Key Events

Analytics domain primarily **consumes** events from all other domains:

| Consumed Event | From Domain | Metric Updated |
|---|---|---|
| `InvoicePaid` | Finance | Revenue metrics |
| `DealClosed` | CRM | Win rate, pipeline velocity |
| `TaskCompleted` | Projects | Team velocity |
| `EmailCampaignSent` | Marketing | Campaign performance |
| `CheckoutCompleted` | E-commerce | GMV, conversion rate |
| `EmployeeHired` | HR | Headcount metrics |

---

## Permissions Prefix

`analytics.dashboards.*` · `analytics.reports.*` · `analytics.kpis.*`  
`analytics.export.*` · `analytics.audit.*` · `analytics.ai.*`

---

## Competitors Displaced

Tableau · Power BI · Looker · Metabase · Sisense · Mixpanel

---

## Related

- [[MOC_Domains]]
- [[MOC_AI]] — AI Insights Engine lives here; AI & Automation domain provides the infrastructure
- [[concept-event-driven]] — analytics is the primary consumer of cross-domain events
