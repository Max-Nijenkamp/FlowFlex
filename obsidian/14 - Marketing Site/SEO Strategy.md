---
tags: [flowflex, marketing, seo, search, organic]
domain: Marketing Site
status: planned
last_updated: 2026-05-07
---

# SEO Strategy

Organic search is the long-term moat. Paid ads can be turned off. A blog that ranks for "best HR software for SMBs" keeps generating leads forever. This document covers both technical SEO and content SEO.

## Goals

1. Rank top 5 for 10 high-intent keywords within 12 months of launch
2. Module pages rank for tool-replacement terms ("hr software", "invoicing tool")
3. Comparison pages capture bottom-of-funnel competitor searches
4. Blog captures top-of-funnel via problem-aware searches

## Keyword Strategy

### Primary Keywords (High Intent — Decision Stage)

| Keyword | Monthly Volume (est.) | Difficulty | Target Page |
|---|---|---|---|
| all-in-one business software | 1,200 | High | `/features` |
| business management software | 4,400 | High | `/features` |
| modular business platform | 200 | Low | Homepage |
| replace bamboohr | 300 | Medium | `/compare/vs-bamboohr` |
| jira alternative | 8,100 | High | `/compare/vs-jira` |
| xero alternative | 2,400 | Medium | `/compare/vs-xero` |
| hubspot alternative | 6,600 | High | `/compare/vs-hubspot` |
| all in one crm hr finance software | 100 | Low | Homepage |

### Module-Level Keywords (Mid Intent — Feature Research)

| Keyword | Target Page |
|---|---|
| hr software small business | `/modules/hr` |
| employee onboarding software | `/modules/hr/onboarding` |
| leave management software | `/modules/hr/leave-management` |
| payroll software uk | `/modules/hr/payroll` |
| project management software | `/modules/projects` |
| time tracking software teams | `/modules/projects/time-tracking` |
| invoicing software small business | `/modules/finance/invoicing` |
| expense management software | `/modules/finance/expense-management` |
| crm software small business | `/modules/crm` |
| sales pipeline software | `/modules/crm/sales-pipeline` |
| email marketing software | `/modules/marketing/email-marketing` |
| inventory management software | `/modules/operations/inventory` |

### Long-Tail Keywords (Top-of-Funnel — Blog)

Target via blog content clusters:
- "how to manage employee onboarding without spreadsheets"
- "what is the best alternative to jira for small teams"
- "how to run payroll for a 10-person company"
- "replace multiple saas tools with one platform"
- "bamboohr vs rippling vs [competitor]"
- "best business software for growing companies"

---

## Technical SEO Checklist

### On-Page

- [ ] Every page has a unique `<title>` (max 60 chars) and `<meta description>` (max 155 chars)
- [ ] Title format: `{Page Name} — FlowFlex` for inner pages, `FlowFlex — {tagline}` for homepage
- [ ] H1 on every page — exactly one per page
- [ ] Heading hierarchy never skips levels (h1 → h2 → h3)
- [ ] All images have descriptive `alt` attributes (not "image1.png")
- [ ] Internal links use descriptive anchor text (not "click here")
- [ ] Canonical `<link rel="canonical">` on every page
- [ ] No duplicate content (especially watch `/modules` vs `/features` overlap)
- [ ] URL slugs are lowercase, hyphen-separated, descriptive, ≤ 5 words

### Technical

- [ ] HTTPS everywhere, HSTS header
- [ ] `sitemap.xml` auto-generated, submitted to Google Search Console
- [ ] `robots.txt` blocks `/admin/` and `/app/` paths
- [ ] 301 redirects for all old/moved pages (never 302 for permanent moves)
- [ ] No soft 404s (pages that return 200 but show "not found" content)
- [ ] Pagination handled correctly (`?page=N` not `?p=N`, rel="next"/"prev" where needed)
- [ ] Breadcrumb structured data on all inner pages
- [ ] `hreflang` added if/when multilingual versions launch
- [ ] Mobile-first responsive design — tested on real devices
- [ ] Core Web Vitals all green (LCP < 2.5s, CLS < 0.1, FID/INP < 200ms)
- [ ] No render-blocking CSS/JS on critical path
- [ ] Server-side rendering (Blade) — no client-side-only content that bots can't crawl
- [ ] All internal links crawlable (`<a href>` — not JS-only navigation)

### Structured Data (JSON-LD)

Add the following schema types:

| Page | Schema Type |
|---|---|
| Homepage | `Organization`, `WebSite` (with `SearchAction`) |
| Pricing | `SoftwareApplication` with `Offer` array |
| Blog posts | `Article` |
| Help centre articles | `FAQPage` or `Article` |
| Comparison pages | `FAQPage` |
| FAQ sections | `FAQPage` |
| About page | `Organization`, `Person` (founders) |
| Careers | `JobPosting` per role |

**Organization schema (all pages, in `<head>`):**
```json
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "FlowFlex",
  "url": "https://flowflex.com",
  "logo": "https://flowflex.com/images/logo.png",
  "sameAs": [
    "https://linkedin.com/company/flowflex",
    "https://twitter.com/flowflex"
  ],
  "contactPoint": {
    "@type": "ContactPoint",
    "contactType": "sales",
    "email": "hello@flowflex.com"
  }
}
```

### Open Graph + Twitter Cards

Every page must have OG + Twitter meta. Use a Blade component `<x-meta-social>`.

```html
<meta property="og:type" content="website">
<meta property="og:site_name" content="FlowFlex">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $ogImage ?? asset('images/og-default.png') }}">
<meta property="og:url" content="{{ request()->url() }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@flowflex">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $ogImage ?? asset('images/og-default.png') }}">
```

OG image dimensions: 1200×630px. Create:
- Default OG image (logo + tagline on ocean-900 background)
- Blog post OG images (auto-generated with post title — use Browsershot or a canvas-based approach)
- Module page OG images (module name + domain colour)

---

## Content SEO

### Content Clusters

Organise blog content in topical clusters. Each cluster has one **pillar page** and many **cluster pages**.

**Cluster 1: Business Software Stack**
- Pillar: "The complete guide to replacing your business software stack" (`/blog/replace-business-software-stack`)
- Clusters: "How to replace BambooHR", "How to consolidate your SaaS tools", "The real cost of using 10 different tools"

**Cluster 2: HR for SMBs**
- Pillar: "HR software for small and medium businesses: the complete guide"
- Clusters: Onboarding, payroll, leave management, performance reviews — each as separate blog posts

**Cluster 3: Project Management**
- Pillar: "Project management software: what teams actually need"
- Clusters: Time tracking, task management, Gantt vs Kanban, agile for non-tech teams

**Cluster 4: Finance & Accounting**
- Pillar: "Small business accounting and invoicing: what software do you need?"
- Clusters: Invoicing, expense management, VAT returns, financial reporting

**Cluster 5: CRM & Sales**
- Pillar: "CRM software for growing businesses: a practical guide"
- Clusters: Sales pipeline, shared inbox, customer support

### Internal Linking Rules

- Every module page links to 2–3 related module pages ("Works with")
- Every blog post links to at least 1 relevant module page
- Every comparison page links to the homepage and the relevant module page
- Help centre articles link back to the relevant module pages on the marketing site
- Homepage links to all 13 domain overview pages

### Link Building Strategy

1. **Product directories:** Submit to G2, Capterra, GetApp, Product Hunt, Trustpilot
2. **HARO / journalist outreach:** Respond to journalists asking about SaaS/SMB software
3. **Guest posts:** Write for SMB-focused publications (e.g. Startups.co.uk, TheNextWeb)
4. **Partner pages:** Integration partners link back (Stripe, Slack, Google Workspace)
5. **Testimonials from customers:** Get customers to mention FlowFlex on their own sites

---

## Local/Geo SEO

If targeting specific countries (Netherlands, UK, etc.):

- Add country-specific landing pages (e.g. `/nl/hr-software`, `/uk/invoicing-software`)
- `hreflang` tags for language/region targeting
- Register with Google Business Profile (for office address)
- Local schema (`LocalBusiness`) if applicable
- Currency on pricing page should be locale-aware (€ for NL/EU, £ for UK)

---

## Search Console & Monitoring

- Submit `sitemap.xml` to Google Search Console on launch
- Submit to Bing Webmaster Tools
- Monitor Core Web Vitals report in Search Console monthly
- Set up rank tracking (Ahrefs / Semrush) for primary 30 keywords from day 1
- Track impressions and CTR — if CTR low but impressions high, rewrite meta titles
- Crawl errors: review weekly for 3 months, then monthly

---

## SEO for Admin Panel

Module pages on the marketing site must have editable SEO fields managed in the Admin Panel:
- Custom `<title>` (override default)
- Custom `<meta description>`
- Custom OG image
- Canonical URL override
- Indexing flag (noindex for draft/unpublished pages)

See [[Admin Panel CMS]] for full content management spec.

## Related

- [[SEM & Paid Advertising]]
- [[Blog & Content Strategy]]
- [[Features & Modules Pages]]
- [[Admin Panel CMS]]
- [[Page Structure & Sitemap]]
