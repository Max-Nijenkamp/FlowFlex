---
tags: [flowflex, marketing, public-site, overview]
domain: Marketing Site
status: planned
last_updated: 2026-05-07
---

# Marketing Site Overview

Everything a prospect sees before they log in. The marketing site is the conversion engine — it must answer "what is this, is it for me, what does it cost, can I trust it" before the visitor hits the CTA.

## Goal

Convert qualified SMB decision-makers into demo requests or trial signups. Secondary goal: SEO-driven inbound from teams actively searching for alternatives to tools like BambooHR, Jira, Xero, HubSpot, and Salesforce.

## What Exists Outside the App

| Section | Purpose |
|---|---|
| Homepage (`/`) | First impression, value prop, social proof |
| Pricing (`/pricing`) | Plan tiers, module pricing, FAQ |
| Features overview (`/features`) | All 13 domains at a glance |
| Module pages (`/modules/{slug}`) | Per-domain and per-module deep dives |
| About (`/about`) | Company story, mission, team |
| Blog (`/blog`) | Content marketing, SEO, thought leadership |
| Blog post (`/blog/{slug}`) | Individual articles |
| Request demo (`/demo`) | Lead capture, qualification form |
| Help centre (`/help`) | Public-facing documentation |
| Changelog (`/changelog`) | Release notes, product updates |
| Careers (`/careers`) | Job listings |
| Contact (`/contact`) | Sales, support, press enquiries |
| Status (`/status`) | Live uptime, incident history |
| Privacy Policy (`/legal/privacy`) | GDPR-compliant privacy policy |
| Terms of Service (`/legal/terms`) | Legal agreement for use of the platform |
| Cookie Policy (`/legal/cookies`) | Cookie usage, consent management |
| Data Processing Agreement (`/legal/dpa`) | B2B GDPR processor agreement |
| Acceptable Use Policy (`/legal/aup`) | What tenants may/may not do |
| Security (`/security`) | Security practices, certifications, responsible disclosure |
| Partners (`/partners`) | Integration partners, resellers, affiliates |
| Sitemap (`/sitemap.xml`) | Machine-readable sitemap |
| Robots (`/robots.txt`) | Crawl rules |

## Tech Approach

Marketing site is built inside the **same Laravel application** as the platform. No separate frontend framework.

- **Blade + Livewire** for all public pages
- **Tailwind CSS** (same design tokens as the app — same brand, consistent look)
- **Alpine.js** for lightweight interactivity (FAQ accordions, pricing toggle, mobile menu)
- **No React/Vue** on the marketing site — keep it fast, server-rendered, SEO-friendly
- Pages live in `resources/views/marketing/`
- Routes defined in `routes/web.php` under a `public` prefix group
- Laravel Folio considered for page routing (evaluate at implementation)

## Content Management

All marketing content (blog, testimonials, pricing copy, team profiles, FAQ entries, demo leads) is managed through the **Admin Panel** at `/admin`. See [[Admin Panel CMS]].

## Performance Requirements

- Lighthouse score ≥ 90 on all four metrics (Performance, Accessibility, Best Practices, SEO)
- LCP < 2.5s on mobile
- CLS < 0.1
- No layout shift from font loading (use `font-display: swap` + preload)
- Images: WebP with fallback, `loading="lazy"` on below-fold, `width`/`height` always set
- No third-party scripts blocking render (GTM in deferred mode, analytics async)

## Analytics & Tracking

- **Google Analytics 4 (GA4)** — primary analytics
- **Google Tag Manager (GTM)** — tag container, fires only after cookie consent
- **Hotjar** (or Clarity) — session recordings, heatmaps (GDPR consent required)
- **Facebook Pixel** — for retargeting via Meta Ads (consent required)
- **LinkedIn Insight Tag** — B2B retargeting (consent required)
- All tracking events defined in [[SEM & Paid Advertising]]
- Cookie consent managed via a first-party consent modal (no CookieBot/OneTrust to avoid CLS and extra scripts)

## Conversion Events

| Event | Trigger |
|---|---|
| `demo_request_started` | User opens demo form |
| `demo_request_submitted` | Demo form successfully submitted |
| `pricing_plan_clicked` | User clicks a plan CTA on pricing page |
| `module_page_viewed` | User lands on a module/feature page |
| `blog_post_read` | User scrolls 75%+ of a blog post |
| `cta_clicked` | Any primary CTA |
| `help_searched` | User submits search in help centre |

## Related

- [[Page Structure & Sitemap]]
- [[Homepage]]
- [[Pricing Page]]
- [[Features & Modules Pages]]
- [[Demo Request Flow]]
- [[SEO Strategy]]
- [[SEM & Paid Advertising]]
- [[Blog & Content Strategy]]
- [[Legal & Compliance Pages]]
- [[Admin Panel CMS]]
