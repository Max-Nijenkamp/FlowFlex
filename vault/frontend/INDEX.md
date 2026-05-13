---
type: frontend
category: index
color: "#FBBF24"
---

# Frontend

MOC for the public-facing Vue 3 + Inertia frontend. FlowFlex has two rendering contexts:

1. **Filament panels** — for authenticated business users (one panel per domain)
2. **Vue + Inertia public frontend** — for unauthenticated visitors and external portal users

This section covers context 2.

---

## Pages in This Section

| File | What it covers |
|---|---|
| [[frontend/marketing-site]] | SEO-optimised public marketing site — homepage, pricing, features, blog |
| [[frontend/client-portal]] | External-facing self-service portal for clients of FlowFlex tenants |
| [[frontend/public-pages]] | Storefront, booking, learner portal, community pages, public org chart |

---

## Shared Technology

| Concern | Solution |
|---|---|
| Framework | Vue 3 + Inertia.js |
| Styling | Tailwind CSS v4 |
| Build | Vite (code-split per section) |
| Meta / SEO | Inertia `<Head>` per page |
| Auth | Separate guards per portal (`portal`, `learner`, `community`) |
| Images | S3 + Cloudflare CDN |
| Analytics | GTM or native events to Analytics domain |

---

## Related

- [[domains/marketing/INDEX]] — CMS module manages content for the marketing site
- [[domains/crm/INDEX]] — Client Portal is a CRM domain deliverable
- [[domains/ecommerce/INDEX]] — Storefront consumed from E-commerce domain
- [[domains/lms/INDEX]] — Learner portal consumed from LMS domain
- [[domains/community/INDEX]] — Community pages consumed from Community domain
- [[architecture/filament-patterns]] — contrast: Filament panels vs public frontend
