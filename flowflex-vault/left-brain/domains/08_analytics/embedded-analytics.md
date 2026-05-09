---
type: module
domain: Analytics & BI
panel: analytics
cssclasses: domain-analytics
phase: 6
status: planned
migration_range: 450000–499999
last_updated: 2026-05-09
---

# Embedded Analytics

Embed FlowFlex dashboards and charts inside customer-facing portals, external websites, or third-party tools. White-labelled, scoped to specific data. Premium revenue tier — customers pay to give their own customers analytics access.

**Panel:** `analytics`  
**Phase:** 6

---

## Use Cases

- **Client Portal analytics**: client can see their own project metrics, invoice summary, support ticket trends — embedded in Client Portal
- **E-commerce seller dashboard**: marketplace sellers see their own sales data embedded in seller portal
- **Partner reporting**: resellers see their pipeline and revenue embedded in Partner Portal
- **Public dashboards**: company publishes public-facing metrics (e.g. impact report, uptime dashboard)
- **Customer success**: CSM sends client a link to their own usage/health dashboard

---

## Features

### Embed Tokens
- Generate signed embed token per viewer (JWT with: company_id, viewer_id, row-level security filters, expiry)
- Token is short-lived (1h default) — must be refreshed by host app server-side
- Never expose FlowFlex admin credentials in frontend — token is viewer-scoped only

### Row-Level Security
- Filter all queries by viewer's identity automatically
- Config: "when viewer_id = contact.id → filter all data to contact.company_id = X"
- Multi-tenant safe: viewer can never see another tenant's data
- Preview in builder: "view as [specific user]" to test RLS

### Embed Code
```html
<!-- Simple iframe embed -->
<iframe
  src="https://analytics.flowflex.com/embed/dashboard/ABC123?token=JWT_TOKEN"
  width="100%"
  height="600"
  frameborder="0"
></iframe>

<!-- JS SDK (recommended — handles token refresh) -->
<script src="https://cdn.flowflex.com/embed.js"></script>
<flowflex-dashboard
  dashboard-id="ABC123"
  get-token="/api/analytics-token"  <!-- your server endpoint that returns fresh JWT -->
  theme="light"
></flowflex-dashboard>
```

### White-Label
- Remove FlowFlex branding from embedded views (Enterprise plan)
- Custom theme: match host app's colour scheme via CSS variables
- Custom font
- Remove "Powered by FlowFlex" footer

### Supported Embed Types
- Full dashboard
- Single chart/widget
- KPI scorecard
- Data table (with sort/filter, no drill-down)
- Public URL (no token required — for truly public dashboards)

### Interaction
- Configurable: read-only vs allow drill-down
- Date range picker: allow viewer to change date range within allowed window
- Export button: allow viewer to download their own data

---

## Pricing Tier
- Embedded analytics is a premium add-on (per viewer seat or per embedded dashboard)
- Metered: charge per monthly active viewer (MAV) on embedded dashboards

---

## Data Model

```erDiagram
    embed_tokens {
        ulid id PK
        ulid company_id FK
        ulid dashboard_id FK
        string viewer_identifier
        json rls_filters
        timestamp expires_at
        boolean is_public
    }
```

---

## Permissions

```
analytics.embedded.create-embeds
analytics.embedded.manage-rls
analytics.embedded.view-usage
```

---

## Related

- [[MOC_Analytics]]
- [[left-brain/architecture/portal-architecture.md]] — embedded in portals
- [[MOC_CRM]] — client portal analytics
