---
type: product
category: ux
color: "#38BDF8"
---

# UX Principles

---

## Core Principle: One Panel Per Domain

Every business domain in FlowFlex has its own dedicated Filament panel with its own URL path, its own primary colour, and its own navigation structure. There is no single mega-admin where every resource for every domain lives. Users navigate to the domain they need and work within a focused context.

This separation provides three benefits:

1. **Cognitive clarity** — a user working in HR sees only HR navigation, HR colours, and HR resources. They are never confused by unrelated features from another domain appearing in their sidebar.
2. **Module gating** — a panel that is not activated simply does not exist for that company. The URL returns a 403, the navigation link is absent, and there is no empty shell to confuse users.
3. **Independent deployment** — each panel's Filament resources, pages, and widgets can be developed, tested, and released independently without touching other domains.

---

## Panel Directory

| Panel Name | Path | Filament Color | Primary Navigation Groups |
|---|---|---|---|
| Admin (FlowFlex staff) | `/admin` | Gray | Companies, Users, Modules, Billing, System |
| App (tenant workspace) | `/app` | Slate | Dashboard, Settings, Marketplace, Notifications |
| HR & People | `/hr` | Violet | Employees, Leave, Payroll, Recruitment, Onboarding |
| Projects & Work | `/projects` | Indigo | Projects, Kanban, Sprints, Time Tracking, Milestones |
| Finance & Accounting | `/finance` | Emerald | Invoices, Expenses, AP/AR, Reports, Bank Feeds |
| CRM & Sales | `/crm` | Rose | Contacts, Pipeline, Deals, Activities, Quotes |
| Marketing & Content | `/marketing` | Pink | Campaigns, Forms, Sequences, Landing Pages, Analytics |
| Operations | `/operations` | Orange | Purchase Orders, Vendors, Inventory, Production |
| Analytics & BI | `/analytics` | Sky | Dashboards, Reports, Data Sources, KPIs |
| IT & Security | `/it` | Cyan | Assets, Helpdesk, Access, Provisioning |
| Legal & Compliance | `/legal` | Amber | Contracts, DSAR Queue, Policy Library, Compliance |
| E-commerce | `/ecommerce` | Teal | Products, Orders, Customers, Catalogue |
| Communications | `/comms` | Blue | Inbox, Channels, Templates, Broadcast |
| Learning & Development | `/lms` | Green | Courses, Enrolments, Certifications, Learning Paths |
| AI & Automation | `/ai` | Indigo | Workflows, Triggers, Agents, Integrations |
| Community & Social | `/community` | Purple | Members, Posts, Groups, Moderation |
| Workplace & Facility | `/workplace` | Lime | Desks, Meeting Rooms, Visitors, Maintenance |
| Professional Services (PSA) | `/psa` | Fuchsia | Engagements, Timesheets, Billing, Resources |
| Product-Led Growth | `/plg` | Sky | Feature Flags, Experiments, In-App Guides, NPS |
| Business Travel | `/travel` | Cyan | Trips, Bookings, Expenses, Duty of Care |
| ESG & Sustainability | `/esg` | Green | Carbon Tracking, ESG Reports, Supplier Ratings |
| Real Estate & Property | `/realestate` | Stone | Properties, Leases, Maintenance, Occupancy |
| Customer Success | `/cs` | Blue | Accounts, Health Scores, Tickets, Playbooks |
| Subscription Billing | `/billing` | Violet | Subscriptions, Revenue, Dunning, MRR Reports |
| Procurement & Spend | `/procurement` | Amber | Requisitions, POs, Vendors, Spend Analytics |
| FP&A | `/fpa` | Emerald | Budgets, Forecasts, Scenarios, Consolidation |
| Events Management | `/events` | Rose | Events, Registrations, Tickets, Venues |
| Document Management | `/dms` | Slate | Documents, Folders, Approvals, Templates |
| Whistleblowing & Ethics | `/ethics` | Red | Reports, Investigations, Policy, Disclosures |
| Field Service | `/field` | Orange | Jobs, Technicians, Scheduling, Parts |
| Pricing Management | `/pricing` | Purple | Price Lists, Rules, Approvals, CPQ |
| Enterprise Risk | `/risk` | Red | Risk Register, Controls, Incidents, Audits |
| Support & Help Desk | `/support` | Orange | Inbox, Knowledge Base, Live Chat, SLA, Automations |
| Omnichannel Inbox | `/inbox` | Blue | Shared Inbox, WhatsApp, Email, SMS, Social, Automations |
| Partner & Channel | `/partners` | Violet | Partners, Deal Registration, Commissions, Portal, Affiliates |

---

## Sidebar

- **Background**: dark (`#111827`, Gray-900) — consistent across all panels
- **Typography**: Inter font, sidebar labels at 13px / medium weight
- **Collapsible**: collapses to icon-only (64px wide) on desktop via `sidebarCollapsibleOnDesktop()`
- **Active indicator**: the domain's primary colour as a left-border accent on the active navigation item
- **Navigation groups**: collapsible sections within the sidebar; group headings are uppercase 11px labels
- **Icons**: Heroicons v2 outline by default, solid on active state

---

## Module-Gated Navigation

Every Filament resource and page within a domain panel implements `canAccess()`. The method checks two conditions: the authenticated user has the required permission, and the company has an active subscription to that module.

```php
public static function canAccess(): bool
{
    return Auth::check()
        && Auth::user()->can('hr.employees.view-any')
        && BillingService::hasModule('hr.employees');
}
```

If the module is not active, the resource does not appear in the sidebar and its URL returns a 403. There is no empty menu item, no "upgrade to unlock" placeholder. The panel simply does not surface what the company has not activated.

---

## Navigation Principles

**Speed above all**: navigation between panels is a hard link (full page load is acceptable). Navigation within a panel is Livewire-rendered with no full reload. Actions within a resource (create, edit, delete) use Filament's built-in Livewire components for instant feedback.

**No spinners for primary actions**: common actions (creating a record, updating a field, approving a request) must not show a full-page spinner. Use optimistic UI where possible. Skeleton screens replace spinners when lists take more than 150ms to load.

**Consistent interaction patterns**: every domain panel follows the same Filament list → create → edit → view flow. Users who know how to manage employees in HR can manage contacts in CRM without relearning the interface. Consistency is a feature.

**Keyboard accessibility**: Filament 5 provides keyboard navigation by default. Custom pages and widgets must not break tab order or focus management.

**Dark mode**: all panels support dark mode via Filament's `darkMode(Feature::Enabled)`. Domain colours are tested for WCAG AA contrast in both light and dark contexts before registration.
