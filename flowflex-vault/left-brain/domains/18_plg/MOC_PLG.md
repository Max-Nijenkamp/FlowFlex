---
type: moc
domain: Product-Led Growth
panel: plg
cssclasses: domain-plg
phase: 7
color: "#0369A1"
last_updated: 2026-05-09
---

# Product-Led Growth (PLG) — Map of Content

For SaaS companies building their own products. Feature flags, in-app onboarding tours, product usage analytics, changelog, in-app NPS, and user segmentation. Replaces LaunchDarkly, Appcues, Pendo, and Beamer.

**Panel:** `plg`  
**Phase:** 7  
**Migration Range:** `890000–909999`  
**Colour:** Sky-700 `#0369A1` / Light: `#E0F2FE`  
**Icon:** `heroicon-o-rocket-launch`

---

## Why This Domain Exists

A significant portion of FlowFlex's ICP are SaaS founders and product teams. They need PLG tooling to run their own products. By including this domain, FlowFlex becomes the platform that SaaS companies use to run their ENTIRE business — including their own product operations.

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| Feature Flags | 7 | planned | Boolean + percentage rollouts, environment targeting, kill switches |
| In-App Tours & Onboarding | 7 | planned | No-code overlay tours, checklists, hotspots, product walkthroughs |
| Product Usage Analytics | 7 | planned | Event tracking, funnel analysis, retention curves, feature adoption |
| In-App Changelog & Announcements | 7 | planned | Feature release notes widget, category filters, emoji reactions |
| In-App NPS & Feedback | 7 | planned | Triggered NPS surveys, custom microsurveys, CES, feature voting |
| User Segmentation | 7 | planned | Segment users by usage behaviour, plan, cohort, geography |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `FeatureFlagToggled` | Feature Flags | Notifications (engineer alert), Analytics (track impact) |
| `NPSResponseReceived` | In-App NPS | CRM (update contact score), Analytics |
| `ProductEventTracked` | Usage Analytics | PLG (funnel update), CRM (update health score) |
| `UserSegmentChanged` | Segmentation | Marketing (update email list), PLG (trigger onboarding flow) |

---

## Filament Panel Structure

**Navigation Groups:**
- `Flags` — Feature Flags, Environments, Override Rules
- `Onboarding` — Tours, Checklists, Hotspots
- `Analytics` — Events, Funnels, Retention, Feature Adoption
- `Comms` — Changelog, Announcements, NPS Surveys
- `Segments` — User Segments, Cohorts

---

## Integration Note

PLG modules are designed to be embedded into the customer's own product via:
- JavaScript SDK (drop-in script tag)
- REST API (server-side event tracking)
- Vue/React component library

---

## Permissions Prefix

`plg.flags.*` · `plg.onboarding.*` · `plg.analytics.*`  
`plg.changelog.*` · `plg.nps.*` · `plg.segments.*`

---

## Competitors Displaced

LaunchDarkly · Appcues · Pendo · Amplitude (product analytics) · Beamer · Canny · Hotjar (NPS)

---

## Related

- [[MOC_Domains]]
- [[MOC_CRM]] — NPS scores → customer health
- [[MOC_Analytics]] — product events feed analytics
- [[MOC_Marketing]] — segments → email marketing
