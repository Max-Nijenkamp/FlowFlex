---
type: product
category: ux
color: "#38BDF8"
---

# UX Principles

---

## One Panel Per Domain

Every domain has its own Filament panel at its own URL path, with its own colour and navigation. No mega-admin.

Benefits:
- **Cognitive clarity** — HR users see only HR navigation
- **Module gating** — inactive panel simply does not exist; URL returns 403
- **Independent development** — each panel's resources can be released independently

---

## Panel Directory

21 panels: `/admin` + `/app` + 19 domain panels. Procurement hosted in `/operations`, Customer Success in `/crm`.

| Panel | Path | Filament Color | Hosts |
|---|---|---|---|
| Admin (FlowFlex staff) | `/admin` | Gray | FlowFlex internal |
| App (tenant workspace) | `/app` | Slate | Core Platform |
| HR & People | `/hr` | Violet | hr |
| Finance & Accounting | `/finance` | Emerald | finance |
| CRM & Sales | `/crm` | Rose | crm + customer-success |
| Projects & Work | `/projects` | Indigo | projects |
| Communications | `/comms` | Blue | communications |
| Support & Help Desk | `/support` | Orange | support |
| Document Management | `/dms` | Slate | dms |
| Marketing | `/marketing` | Pink | marketing |
| Operations | `/operations` | Orange | operations + procurement |
| Analytics & BI | `/analytics` | Sky | analytics |
| IT & Security | `/it` | Cyan | it |
| Legal & Compliance | `/legal` | Amber | legal |
| E-commerce | `/ecommerce` | Teal | ecommerce |
| Learning & Dev | `/lms` | Green | lms |
| AI & Automation | `/ai` | Indigo | ai |
| Workplace | `/workplace` | Lime | workplace |
| Events | `/events` | Rose | events |

---

## Sidebar

- Background: `#111827` (Gray-900) — consistent across all panels
- Collapsible to icon-only on desktop (`sidebarCollapsibleOnDesktop()`)
- Active indicator: domain primary colour as left-border accent
- Icons: Heroicons v2 outline (default), solid on active state

---

## Module-Gated Navigation

Every resource and page implements `canAccess()` — checks permission AND active module subscription:

```php
public static function canAccess(): bool
{
    return Auth::check()
        && Auth::user()->can('hr.employees.view-any')
        && BillingService::hasModule('hr.employees');
}
```

If module is inactive: no sidebar link, URL returns 403. No "upgrade to unlock" placeholder.

---

## Navigation Principles

- **Panel-to-panel**: hard link (full page load acceptable)
- **Within panel**: Livewire-rendered, no full reload
- **No spinners for primary actions**: optimistic UI where possible; skeleton screens for lists >150ms
- **Consistent flow**: list → create → edit → view — same in every domain
- **Keyboard accessibility**: tab order and focus management must not break in custom pages/widgets
- **Dark mode**: all panels support `darkMode(Feature::Enabled)`; domain colours tested for WCAG AA

---

## Custom Pages (Not Everything Is CRUD)

Some screens require custom Filament layouts beyond standard resource list/create/edit:

- Kanban board (Projects)
- Gantt chart (Projects)
- Calendar view (HR Leave, Events)
- Dashboard widgets (Analytics, Finance)
- Client portal views (CRM)
- Learner portal (LMS)

See [[architecture/patterns/custom-pages]] for how to build custom Filament pages and layouts.

For the **public-facing** side (marketing site, client portal, learner portal) — Vue 3 + Inertia. See [[frontend/_index]].
