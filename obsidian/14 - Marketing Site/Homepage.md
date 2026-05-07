---
tags: [flowflex, marketing, homepage, landing]
domain: Marketing Site
status: planned
last_updated: 2026-05-07
---

# Homepage

The homepage must do one job: make a qualified SMB decision-maker say "this is exactly what I've been looking for" and click the CTA. Every section earns its place or gets cut.

## URL

`https://flowflex.com/`

## Meta

```
<title>FlowFlex — One Platform. Every Tool. Your Way.</title>
<meta name="description" content="FlowFlex replaces your stack of disconnected tools with one modular platform. HR, finance, projects, CRM, and 99+ more — activate only what you need.">
<link rel="canonical" href="https://flowflex.com/">
```

## Page Structure

### 1. Navigation Bar (sticky)

- **Left:** FlowFlex logo (horizontal lockup)
- **Centre:** `Features` · `Modules` (dropdown) · `Pricing` · `Blog`
- **Right:** `Log in` (ghost button) · `Request Demo` (primary button, `ocean-500`)
- Mobile: hamburger → full-screen nav overlay
- Behaviour: transparent on load, solid `slate-900` bg after 60px scroll
- No announcement banner on launch — add once there's something worth announcing

### 2. Hero Section

**Layout:** Centred, full-width, `slate-100` background, `space-24` top padding

**Content:**
```
OVERLINE: "One platform. Every tool."
H1: Your business, your tools — in flow.
BODY LG: Replace 8 disconnected tools with one modular platform.
         HR, finance, projects, CRM, and 99+ modules — activate only
         what you need. One login. One data layer. One bill.
CTA ROW: [Request a Demo →]  [See all modules]
SUBTEXT: No credit card required · Setup in minutes · Cancel any time
```

**Visual:** Product screenshot or short looping video showing the workspace dashboard — ideally showing the module switcher and a populated dashboard. Use real-looking dummy data, not Lorem Ipsum.

**Tone:** Confident, not hypey. "Replace 8 tools" is specific and believable. Avoid "the most powerful" or "revolutionary".

---

### 3. Problem Statement Section

**Layout:** 3-column icon cards, `white` background, `space-20` padding

**Headline:** "Sound familiar?"

**3 cards:**
1. **Too many tools** — "Your team uses BambooHR, Jira, Xero, HubSpot, Slack, and 4 more. None of them talk to each other."
2. **Paying for waste** — "Salesforce charges for 200 features you'll never touch. SAP requires a consultant just to add a user."
3. **Data everywhere** — "The same customer exists in 3 different tools as 3 different records. Reporting is a spreadsheet."

**End line:** "FlowFlex is built differently."

---

### 4. How It Works Section

**Layout:** 3-step horizontal flow with connector lines, `ocean-50` background

**Headline:** "One system. Built your way."

**Steps:**
1. **Activate your modules** — Choose from 99+ modules across HR, finance, projects, CRM, and more. Pay only for what you switch on.
2. **Your team, your data** — Everything shares one data layer. Your employee is your CRM contact is your project assignee — one record, everywhere.
3. **Scale as you grow** — Add modules as your business grows. No migrations, no new logins, no data sync headaches.

---

### 5. Module Grid / "What's Inside" Section

**Layout:** Full-width, white background

**Headline:** "Everything your business needs. In one place."
**Subtext:** "13 domains. 99+ modules. Only pay for what you activate."

**Display:** Horizontal scroll or grid of domain cards. Each card shows:
- Domain colour accent (left border or icon background)
- Domain icon
- Domain name
- 3–4 key module names
- `Explore →` link to `/modules/{domain}`

**Domains shown:**
HR & People · Projects & Work · Finance · CRM & Sales · Marketing · Operations · Analytics · IT & Security · Legal · E-commerce · Communications · Learning & Development

**Bottom CTA:** "See all 99+ modules →" linking to `/features`

---

### 6. Benefits / Why FlowFlex Section

**Layout:** 2-column alternating (text left/right, visual opposite), white background

**Headline:** "Built to replace your whole stack"

**Benefit 1 — Unified data**
- Text: "One employee record. One customer record. One project. Every module reads from the same data — no sync, no duplicates, no spreadsheet bridges."
- Visual: Diagram showing one record powering HR, CRM, Projects

**Benefit 2 — Modular pricing**
- Text: "Activate modules like apps on your phone. You pay per module, not for the entire platform. Pause a module and billing pauses with it."
- Visual: Module toggle UI screenshot

**Benefit 3 — Serious security**
- Text: "Finance, HR, and legal data lives in FlowFlex. Encrypted at rest and in transit, granular role-based permissions, full audit trail of every action."
- Visual: Permission/audit trail screenshot

**Benefit 4 — Built for your team size**
- Text: "Works for a 5-person team and scales to 500 without re-platforning. Starter plan up to 10 users. Pro for growing teams. Enterprise for complex orgs."
- Visual: Team size illustration

---

### 7. Social Proof Section

**Layout:** White background, three zones

**Zone A — Logos bar:**
- Headline: "Trusted by businesses that outgrew the tool stack"
- Row of client logos (greyscale, full-opacity on hover)
- Placeholder until launch: show 5–6 "early access partner" logos or leave this section out until real logos available

**Zone B — Testimonial carousel:**
- 3 testimonials, 2 visible at a time, auto-rotate with pause on hover
- Each: photo + name + role + company + quote (max 2 sentences)
- Quotes managed in Admin Panel CMS

**Zone C — Numbers:**
- Stat 1: `99+` modules
- Stat 2: `13` business domains
- Stat 3: `300+` individual features
- Stat 4: `1` data layer
- (Replace with real customer numbers as soon as available: customers, countries, etc.)

---

### 8. Comparison Section

**Layout:** 3-column table, `slate-100` background

**Headline:** "FlowFlex vs the stack you're replacing"

| Feature | FlowFlex | Legacy Stack |
|---|---|---|
| One login | ✅ | ❌ (5+ logins) |
| Unified data | ✅ | ❌ (manual sync) |
| Pay only for what you use | ✅ | ❌ (bundle pricing) |
| Works across all business functions | ✅ | ❌ (multiple vendors) |
| Single audit trail | ✅ | ❌ |
| Module-level permissions | ✅ | ❌ |

**CTA below table:** `See detailed comparisons →` linking to `/compare/vs-bamboohr` (and others)

---

### 9. Pricing Teaser Section

**Layout:** Centred, `ocean-900` dark background (white text)

**Headline:** "Simple pricing. No surprises."
**Body:** "Three plans. Unlimited modules on Pro and Enterprise. Annual billing saves 2 months."

**3 plan cards (condensed — full detail on `/pricing`):**
- Starter: from €X/mo · up to 10 users · up to 5 modules
- Pro: from €X/mo · up to 100 users · unlimited modules
- Enterprise: Custom · unlimited everything

**CTA:** `See full pricing →`

---

### 10. Final CTA Section

**Layout:** Full-width, `ocean-500` background, white text

**Headline:** "Ready to bring your business into flow?"
**Body:** "Book a 30-minute demo. We'll walk you through the modules that replace your current stack."
**CTA:** `[Request your demo →]` — white button, `ocean-900` text

**Trust signals below button:**
- "No credit card required"
- "Response within 24 hours"
- "Cancel any time"

---

### 11. Footer

See [[Footer]] section below.

---

## Footer (Global — All Pages)

**Layout:** 4-column, `ocean-950` background, `ocean-50` text

**Column 1 — FlowFlex:**
- Logo (white variant)
- Tagline: "Your business, your tools — in flow."
- Social icons: LinkedIn · Twitter/X · YouTube (link to channel when created)
- Copyright: © 2026 FlowFlex Ltd. All rights reserved.

**Column 2 — Product:**
- Features
- Modules (dropdown or link to /features)
- Pricing
- Changelog
- Status
- Request Demo

**Column 3 — Company:**
- About
- Blog
- Careers
- Partners
- Contact
- Security

**Column 4 — Legal:**
- Privacy Policy
- Terms of Service
- Cookie Policy
- Data Processing Agreement
- Acceptable Use Policy

**Bottom bar (below columns):**
- Cookie preferences link (opens consent modal)
- VAT: FlowFlex Ltd · [Company number] · Registered in [Country]

## Related

- [[Pricing Page]]
- [[Demo Request Flow]]
- [[Features & Modules Pages]]
- [[Marketing Site Overview]]
