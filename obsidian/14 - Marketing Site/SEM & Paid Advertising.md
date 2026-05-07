---
tags: [flowflex, marketing, sem, sea, ppc, google-ads, paid]
domain: Marketing Site
status: planned
last_updated: 2026-05-07
---

# SEM & Paid Advertising

SEA (Search Engine Advertising) = paid search. The complement to organic SEO — captures high-intent traffic immediately while organic rankings build. Keep budgets tight and ROI-focused at launch.

## Channels

| Channel | Use Case | Priority |
|---|---|---|
| Google Search Ads | High-intent keyword capture | P0 |
| Google Display Ads | Remarketing to site visitors | P1 |
| LinkedIn Ads | B2B targeting by company size / job title | P1 |
| Meta Ads (Facebook/Instagram) | Remarketing + lookalike audiences | P2 |
| YouTube / Video | Brand awareness (Phase 2) | P3 |

## Google Search Ads

### Account Structure

```
Account: FlowFlex
├── Campaign: Brand
│   └── Ad Group: Brand Terms
├── Campaign: Competitor
│   ├── Ad Group: vs BambooHR
│   ├── Ad Group: vs Jira
│   ├── Ad Group: vs Xero
│   ├── Ad Group: vs HubSpot
│   └── Ad Group: vs Salesforce
├── Campaign: Module — HR
│   ├── Ad Group: HR Software
│   ├── Ad Group: Payroll Software
│   ├── Ad Group: Onboarding Software
│   └── Ad Group: Leave Management
├── Campaign: Module — Projects
│   ├── Ad Group: Project Management
│   └── Ad Group: Time Tracking
├── Campaign: Module — Finance
│   ├── Ad Group: Invoicing Software
│   └── Ad Group: Accounting Software
├── Campaign: Module — CRM
│   ├── Ad Group: CRM Software
│   └── Ad Group: Sales Pipeline
└── Campaign: Generic Platform
    ├── Ad Group: All-in-One Business Software
    └── Ad Group: SaaS Platform Alternatives
```

---

### Brand Campaign

**Purpose:** Protect own brand. Prevent competitors bidding on "FlowFlex".
**Keywords:** `flowflex`, `flow flex`, `flowflex software`, `flowflex app`
**Budget:** Low (€5–10/day) — these clicks are near-free but important to own
**Landing page:** Homepage

---

### Competitor Campaigns

**Purpose:** Capture people searching for alternatives to named competitors.

**Example — BambooHR:**

Keywords:
- `bamboohr alternative`
- `bamboohr alternatives`
- `replace bamboohr`
- `better than bamboohr`
- `bamboohr competitor`

Ad headline 1: `BambooHR Alternative — FlowFlex`
Ad headline 2: `HR + 12 More Modules. One Login.`
Ad headline 3: `Free 14-Day Trial. No Credit Card.`
Description 1: `FlowFlex replaces BambooHR with HR, payroll, leave, onboarding — plus finance, CRM, and 99+ more modules.`
Description 2: `Pay only for what you activate. Starts from €X/month. Book a free demo today.`
**Landing page:** `/compare/vs-bamboohr`

*Repeat pattern for: Jira, Xero, HubSpot, Salesforce, Monday, Notion, QuickBooks*

**Important:** Do not use competitor trademarks in ad headlines — use them in keywords only. Review Google's trademark policy per competitor.

---

### Module Campaigns

**HR Software Ad Group:**

Keywords (exact + phrase match):
- `[hr software for small business]`
- `"hr management software"`
- `[hr software smb]`
- `"best hr software"`
- `[human resources software]`

Ad:
- Headline 1: `HR Software Built to Scale`
- Headline 2: `Employees, Payroll, Leave — One Place`
- Headline 3: `14-Day Free Trial · No Card Needed`
- Description 1: `FlowFlex HR covers employee profiles, onboarding, leave, payroll, performance and more.`
- Description 2: `Activate only the HR modules you need. Works with finance, projects, and 10+ other domains.`
- **Landing page:** `/modules/hr`

---

### Generic Platform Campaigns

Keywords:
- `all in one business software`
- `business management software`
- `replace saas stack`
- `unified business platform`
- `modular business software`

Landing page: `/features`

---

## Ad Extensions (Google)

Use all relevant extensions on every campaign:
- **Sitelinks:** Features · Pricing · Modules · Request Demo
- **Callouts:** "No Credit Card Required" · "14-Day Free Trial" · "GDPR Compliant" · "EU Data Storage"
- **Structured snippets:** Type = "Services": HR Software, Finance, CRM, Projects, Marketing
- **Lead form extension:** Capture name + email directly in the SERP (optional — A/B test)
- **Image extensions:** UI screenshots (where supported)

---

## Bidding Strategy

| Campaign | Bidding Strategy | Notes |
|---|---|---|
| Brand | Manual CPC (low) | Keep costs minimal — brand terms convert cheap |
| Competitor | Target CPA once data | Start manual, optimise toward demo submission |
| Module | Target CPA | Set CPA = max acceptable cost per demo request |
| Generic | Target Impression Share | Brand awareness — cap at 50% to control spend |

**Initial CPA target:** Set conservatively based on LTV. If average contract is €X/year, demo-to-customer rate is Y%, max CPA should be ≤ €X × Y × 30%.

---

## Landing Page Requirements

Every ad campaign must have a matching landing page (not just the homepage). Rules:
- Headline matches the ad headline (message match reduces bounce rate)
- CTA is the demo request form or a link to `/demo`
- Remove site navigation (landing pages can suppress nav to reduce distraction)
- UTM parameters auto-captured in form submission
- Conversion pixel fires on form success state

**UTM parameter convention:**
```
utm_source=google
utm_medium=cpc
utm_campaign={campaign-name}
utm_content={ad-group-name}
utm_term={keyword}
```

---

## Remarketing

### Google Display Remarketing

**Audience segments:**
- "All site visitors" (last 30 days, exclude /admin and /app)
- "Pricing page visitors" (high intent — bid higher)
- "Module page visitors" (segment by domain)
- "Demo page visitors, no conversion" (highest intent — highest bid)
- "Blog readers" (low intent — brand awareness only)

**Ad creative:**
- 300×250, 728×90, 160×600, 320×50, responsive display
- Always include: FlowFlex logo + tagline + CTA "See it in action"
- Use domain colour for the ad they saw (HR visitor → violet accents)

### LinkedIn Retargeting

- Install LinkedIn Insight Tag on all marketing pages
- Retarget: site visitors, pricing page visitors, demo page non-converters
- Audiences: HR directors, Finance managers, CEOs, Operations managers, 50–500 employee companies

---

## LinkedIn Ads (Prospecting)

**Campaign type:** Sponsored Content + Message Ads
**Targeting:**
- Job titles: HR Manager, HR Director, Operations Manager, CFO, CEO, CTO, Head of Finance, Business Owner
- Company size: 50–500 employees
- Industries: Professional services, tech, retail, manufacturing, hospitality
- Geography: Start with NL + UK, expand from there

**Ad formats:**
- Single image ads (feature screenshot + CTA)
- Carousel ads (show 4 modules in one ad)
- Document ads (downloadable guide as lead magnet)

**Lead magnet idea:** "The 2026 SMB Software Stack Guide" — downloadable PDF that benchmarks the cost of running 10 tools vs FlowFlex. Gated behind LinkedIn Lead Gen Form (name, email, company, size). Leads sync to Admin Panel.

---

## Geo Targeting Strategy

### Phase 1 (Launch)

Focus on:
- **Netherlands** — home market, Dutch-language landing page (optional)
- **United Kingdom** — large SMB market, English-language

Exclude: markets where we have no localisation and no support capacity.

### Phase 2

Expand to:
- Belgium, Germany, France (with translated landing pages)
- Add local phone numbers and local payment methods (iDEAL for NL)

---

## Conversion Tracking Setup

| Conversion | Platform | How |
|---|---|---|
| Demo request submitted | GA4 | Event tag in GTM fires on form success state |
| Demo request submitted | Google Ads | Import from GA4 |
| Demo request submitted | LinkedIn | Insight Tag event |
| Demo request submitted | Meta Pixel | `Lead` standard event |
| Pricing page visit | GA4 | Pageview event |
| Module page visit | GA4 | Pageview event with `module` param |

All GTM tags fire only after cookie consent is given.

---

## Budget Guidelines

At launch, allocate carefully:

| Campaign | Monthly Budget (starter) |
|---|---|
| Brand | €50 |
| Competitor (top 3) | €500 |
| HR Module | €300 |
| Projects Module | €200 |
| Generic Platform | €200 |
| LinkedIn Prospecting | €500 |
| **Total** | **~€1,750/mo** |

Review CPAs weekly for first month. Cut anything above 2× target CPA. Double down on what converts.

---

## Reporting

Monthly review:
- Impressions, clicks, CTR per campaign
- Cost per click (CPC) trend
- Conversions (demo requests) per campaign
- Cost per conversion (CPA) vs target
- Conversion rate of landing pages (GA4 data)
- Revenue attributed to paid (from closed demo requests)

Report lives in Admin Panel `/admin/marketing/ads` (Phase 4 — until then, Google Ads + GA4 dashboards).

## Related

- [[SEO Strategy]]
- [[Demo Request Flow]]
- [[Blog & Content Strategy]]
- [[Admin Panel CMS]]
