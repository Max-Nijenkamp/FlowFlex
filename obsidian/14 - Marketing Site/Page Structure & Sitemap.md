---
tags: [flowflex, marketing, sitemap, pages, routes]
domain: Marketing Site
status: planned
last_updated: 2026-05-07
---

# Page Structure & Sitemap

Every public-facing URL. This is the canonical source for what pages exist, their URL, priority, and purpose.

## URL Structure

```
flowflex.com/
├── (homepage)
├── pricing
├── features
│   └── (overview of all 13 domains)
├── modules/
│   ├── hr
│   ├── hr/employee-profiles
│   ├── hr/onboarding
│   ├── hr/leave-management
│   ├── hr/payroll
│   ├── hr/performance-reviews
│   ├── hr/recruitment
│   ├── hr/scheduling
│   ├── hr/benefits
│   ├── hr/employee-feedback
│   ├── hr/compliance
│   ├── projects
│   ├── projects/task-management
│   ├── projects/project-planning
│   ├── projects/time-tracking
│   ├── projects/document-management
│   ├── projects/knowledge-base
│   ├── projects/agile-sprints
│   ├── finance
│   ├── finance/invoicing
│   ├── finance/expense-management
│   ├── finance/financial-reporting
│   ├── finance/accounts-payable-receivable
│   ├── finance/bank-reconciliation
│   ├── finance/budgeting
│   ├── finance/tax-vat
│   ├── crm
│   ├── crm/contact-management
│   ├── crm/sales-pipeline
│   ├── crm/shared-inbox
│   ├── crm/helpdesk
│   ├── crm/client-portal
│   ├── crm/quotes-proposals
│   ├── marketing
│   ├── marketing/cms-website
│   ├── marketing/email-marketing
│   ├── marketing/forms-lead-capture
│   ├── marketing/seo-analytics
│   ├── marketing/social-media
│   ├── marketing/events-webinars
│   ├── operations
│   ├── operations/inventory
│   ├── operations/asset-management
│   ├── operations/purchasing
│   ├── operations/field-service
│   ├── operations/quality-control
│   ├── analytics
│   ├── analytics/custom-dashboards
│   ├── analytics/report-builder
│   ├── analytics/kpi-tracking
│   ├── it
│   ├── it/asset-management
│   ├── it/helpdesk
│   ├── it/security-compliance
│   ├── legal
│   ├── legal/contract-management
│   ├── legal/policy-management
│   ├── legal/risk-register
│   ├── ecommerce
│   ├── ecommerce/product-catalogue
│   ├── ecommerce/order-management
│   ├── ecommerce/storefront
│   ├── communications
│   ├── communications/internal-chat
│   ├── communications/announcements
│   ├── communications/booking-scheduling
│   ├── lms
│   ├── lms/course-builder
│   ├── lms/skills-matrix
│   └── lms/succession-planning
├── about
├── blog/
│   ├── (listing page)
│   ├── category/{slug}
│   └── {slug} (individual posts)
├── demo
├── contact
├── careers/
│   ├── (listing)
│   └── {slug} (individual role)
├── partners
├── changelog/
│   ├── (listing)
│   └── {slug}
├── help/
│   ├── (search + category listing)
│   ├── {category-slug}
│   └── {category-slug}/{article-slug}
├── status
├── compare/
│   ├── vs-bamboohr
│   ├── vs-jira
│   ├── vs-xero
│   ├── vs-hubspot
│   ├── vs-salesforce
│   ├── vs-notion
│   ├── vs-monday
│   └── vs-microsoft365
├── legal/
│   ├── privacy
│   ├── terms
│   ├── cookies
│   ├── dpa
│   ├── aup
│   └── security
├── sitemap.xml
└── robots.txt
```

## Page Priority by Conversion Impact

| Priority | Page | Why |
|---|---|---|
| P0 | Homepage | First impression, all traffic passes through |
| P0 | Demo request | Primary conversion action |
| P0 | Pricing | Decision-stage page, high intent |
| P1 | Module pages | SEO traffic, feature validation |
| P1 | Comparison pages | High-intent competitor search traffic |
| P1 | Features overview | Category-awareness traffic |
| P2 | Blog | Top-of-funnel, SEO |
| P2 | About | Trust-building, late-stage validation |
| P2 | Help centre | Reduces support load, SEO |
| P3 | Legal pages | Required, not conversion-driving |
| P3 | Changelog | Retention signal for existing users |
| P3 | Careers | Talent pipeline |
| P3 | Status page | Trust signal |

## Redirects & Special Routes

| From | To | Reason |
|---|---|---|
| `/app` | `app.flowflex.com` | Main app subdomain redirect |
| `/login` | `app.flowflex.com/login` | App login |
| `/register` | `/demo` | No self-registration — redirect to demo |
| `/signup` | `/demo` | Same |
| `/trial` | `/demo` | Same |

## Subdomain Map

| Subdomain | Purpose |
|---|---|
| `flowflex.com` | Marketing site |
| `app.flowflex.com` | The application (all Filament panels) |
| `help.flowflex.com` | Help centre (optional — or `/help` path works) |
| `status.flowflex.com` | Uptime monitoring (BetterUptime / UptimeRobot embed or dedicated page) |
| `api.flowflex.com` | REST API (same app, aliased) |

## Sitemap.xml Structure

Auto-generated via Laravel. Include:
- All static marketing pages (change frequency: monthly, priority: 0.8)
- All published blog posts (change frequency: weekly, priority: 0.7)
- All module pages (change frequency: monthly, priority: 0.9)
- All comparison pages (change frequency: monthly, priority: 0.9)
- Exclude: `/admin/*`, `/app/*`, any authenticated routes

## robots.txt

```
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /app/
Disallow: /api/

Sitemap: https://flowflex.com/sitemap.xml
```

## Related

- [[Marketing Site Overview]]
- [[SEO Strategy]]
- [[Homepage]]
- [[Features & Modules Pages]]
