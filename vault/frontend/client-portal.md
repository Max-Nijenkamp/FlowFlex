---
type: frontend
category: client-portal
color: "#FBBF24"
---

# Client Portal

Customer-facing self-service portal for clients of FlowFlex tenants. Separate authentication guard (`auth:portal`), white-labeled per company. Clients can view their invoices, track projects, manage support tickets, and access shared documents — without any access to the Filament admin panels used by the tenant's team.

---

## Routes

| Route | Page |
|---|---|
| `/portal/login` | Portal login (separate from tenant login) |
| `/portal/forgot-password` | Password reset for portal users |
| `/portal/dashboard` | Client overview dashboard |
| `/portal/invoices` | Invoice list |
| `/portal/invoices/:id` | Invoice detail with pay/download actions |
| `/portal/projects` | Active and completed project list |
| `/portal/projects/:id` | Project detail with timeline and comments |
| `/portal/support` | Support ticket list |
| `/portal/support/:id` | Ticket thread with reply |
| `/portal/documents` | Shared document library |
| `/portal/profile` | Account settings and notification preferences |

---

## Features

### Dashboard

- Open invoices summary: total overdue amount, next due date
- Active projects with completion progress bars
- Open support tickets count with latest update
- Recent document activity feed
- Quick actions: Pay invoice, raise a ticket, upload a document

### Invoices

- Full invoice list, filterable by status: paid, unpaid, overdue
- Download invoice as PDF
- Pay online via Stripe (Stripe Payment Link or embedded Stripe Elements)
- Dispute an invoice (creates an internal support ticket linked to the invoice)

### Projects

- List of active and completed projects
- Task completion percentage per project
- Read-only project timeline (Gantt view, no editing)
- File attachments shared from the project
- Comment thread visible to both the tenant's team and the client

### Support / Helpdesk

- Submit a new support ticket (subject, description, attachments)
- View open and closed ticket history
- Reply to an existing ticket thread
- Upload attachments to a ticket
- CSAT star rating prompt on ticket close

### Documents

- Document library fed from the tenant's Document Management module
- Download files in original format
- E-sign documents if the E-Signature module is enabled for the tenant
- View document approval status and version

### Profile

- Update contact name and email address
- Change password
- Notification preferences: email and push (per event type)
- Connected portal users (if the client company has multiple portal users)

---

## Branding

The portal is fully white-labeled per tenant company:

- Custom subdomain: `portal.clientname.com` or `clientname.flowflex.app` (per DNS setting)
- Company logo in header and browser tab favicon
- Primary accent colour pulled from company branding settings (applied as CSS variable)
- Footer: company name, contact email, company website link
- No FlowFlex branding is shown unless the company explicitly opts in

---

## Authentication

Portal users are stored in a separate `portal_users` table — they are not tenant users:

- Invited by a tenant user via the Client Portal module in the `/crm` panel
- Scoped strictly to their own company's data via portal-level permissions (no cross-company data leakage)
- Password reset uses a dedicated `/portal/forgot-password` flow (separate email template)
- Sessions use the `portal` guard; Filament panel sessions use the `web` guard — the two cannot overlap

---

## Technology

- Vue 3 + Inertia.js + Tailwind CSS v4
- Shared Vite config with the marketing site; separate entry point (`resources/js/portal.js`)
- Tenant branding injected via server-side Inertia shared data (`Inertia::share`)
- Stripe Elements embedded client-side for in-portal payment

---

## Related

- [[frontend/INDEX]] — frontend section overview
- [[frontend/marketing-site]] — public marketing site
- [[frontend/public-pages]] — storefront, booking, learner, community pages
- [[domains/crm/INDEX]] — Client Portal module lives in the CRM domain
- [[domains/finance/INDEX]] — invoices sourced from Finance domain
- [[domains/projects/INDEX]] — project data sourced from Projects domain
- [[domains/dms/INDEX]] — documents sourced from Document Management domain
