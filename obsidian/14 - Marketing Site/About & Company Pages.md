---
tags: [flowflex, marketing, about, company, careers, contact]
domain: Marketing Site
status: planned
last_updated: 2026-05-07
---

# About & Company Pages

Pages that build trust and credibility. Decision-stage buyers always check the About page before committing.

## About Page (`/about`)

### Purpose

Establish credibility, show the humans behind the product, explain why FlowFlex exists. This is not a marketing page — it's a trust page.

### Meta

```
<title>About — FlowFlex</title>
<meta name="description" content="FlowFlex is built by people who got tired of running businesses on 10 disconnected tools. Here's our story.">
```

### Sections

**1. Origin story**
- Who built FlowFlex and why
- The specific frustration that led to it
- Keep it honest and personal — not "we saw an opportunity in the market"
- Example tone: "I spent three years running [previous company] on BambooHR, Jira, Xero, and HubSpot. They never talked to each other. I built FlowFlex because that problem seemed too fundamental not to solve."

**2. Mission & Vision**
- **Mission:** "To give every business one system of record that actually works — modular, connected, and priced fairly."
- **Vision:** "A world where growing a business means spending less time on admin and more time on the work that matters."

**3. Values**
- **Simplicity over features** — We'd rather remove a confusing option than add a switch.
- **Modularity is a feature** — You should never pay for software you don't use.
- **Data integrity above all** — If two modules share a customer, it must be the same record. Always.
- **Honesty in product and in pricing** — No dark patterns, no hidden costs, no misleading comparisons.
- **Speed is respect** — Slow software wastes people's time. That's not acceptable.

**4. Team section**
- Founder(s) first, then early team members
- Each card: photo (professional but not stiff) + name + role + 1-line bio + LinkedIn
- Keep bios human: "Max built product at [X] before founding FlowFlex. He still writes code."
- No stock photos — always real people

**5. Investors / Backers (if applicable)**
- Add when relevant. Leave out until there's something worth saying.

**6. FlowFlex by numbers**
- Modules: 99+
- Domains: 13
- Features: 300+
- Replace with real customer numbers as soon as they exist

**7. CTA**
- "Want to see it in action?" → Request Demo

---

## Contact Page (`/contact`)

### Meta

```
<title>Contact — FlowFlex</title>
<meta name="description" content="Get in touch with the FlowFlex team. Sales, support, press, or anything else.">
```

### Sections

**Contact reasons + routing:**

| Reason | Contact method |
|---|---|
| Book a demo / sales enquiry | Form (goes to demo request queue in admin) |
| Customer support | Link to help centre + `support@flowflex.com` |
| Billing question | `billing@flowflex.com` |
| Press / media enquiry | `press@flowflex.com` |
| Partnership / integration | `partners@flowflex.com` |
| Security / vulnerability | `security@flowflex.com` |
| General / other | `hello@flowflex.com` |

**Form fields (general contact):**
- Name
- Email
- Subject (dropdown: Sales · Support · Billing · Press · Partnership · Other)
- Message
- Submit → creates a contact request in admin panel

**Office:**
- Display registered company address (once confirmed)
- Do not display home address

**Response times:**
- Sales: within 24 hours
- Support: within 48 hours (SLA depends on plan)
- Press: within 48 hours
- Billing: within 48 hours

---

## Careers Page (`/careers`)

### Purpose

Attract talent. Even if hiring is slow, a well-made careers page signals a serious company.

### Meta

```
<title>Careers — FlowFlex</title>
<meta name="description" content="Join the FlowFlex team. We're building the operating system for modern businesses.">
```

### Page Structure

**1. Culture statement (3–4 sentences)**
- What it's like to work at FlowFlex
- What you optimise for as a team
- What you don't do (no crunch culture, no feature factory, etc.)

**2. Perks / benefits**
- Remote-first / flexible hours
- [Benefits as they exist]
- Learning budget
- Hardware budget
- Equity (if applicable)

**3. Open roles listing**
- Filtered by department
- Each listing: title + location/remote + type (full-time/contract) + `View & Apply →`
- If no open roles: "No open roles right now, but we're always interested in great people. Send an email to jobs@flowflex.com."

### Individual Role Page (`/careers/{slug}`)

Fields:
- Role title (H1)
- Department
- Location / Remote
- Type
- Salary range (recommended — improves applicant quality)
- About the role (3–4 paragraphs)
- What you'll do (bulleted)
- What we're looking for (bulleted)
- Nice to have (bulleted)
- How to apply

**Apply via:** Email form on page, or link to a hiring platform (Linear/Ashby/Workable). Store applications in Admin Panel or forward to jobs@flowflex.com.

**Structured data:** `JobPosting` JSON-LD on every individual role page. Required for Google Jobs integration.

---

## Partners Page (`/partners`)

### Purpose

Explain the partner programme for resellers, integration partners, and affiliates.

### Sections

**1. Partner types:**

| Type | Who | What they do | What they get |
|---|---|---|---|
| Reseller | Agencies, consultants, IT firms | Sell FlowFlex to their clients | Revenue share, co-marketing |
| Integration partner | SaaS companies | Build integrations with FlowFlex | Listed in integration directory, technical access |
| Affiliate | Bloggers, communities, influencers | Refer customers via tracked link | Revenue share per conversion |

**2. Benefits per tier (for resellers):**
- Commission structure
- Partner portal access (manage clients, billing)
- Co-marketing opportunities
- Training and certification

**3. CTA:** `Apply to become a partner →` — opens a contact form routed to partners@flowflex.com

---

## Status Page (`/status` or `status.flowflex.com`)

### Purpose

Real-time uptime information. Builds trust. Reduces support noise during incidents.

### Tool Options

- **BetterUptime** (hosted, embeddable) — quickest to implement
- **UptimeRobot** + custom status page (self-hosted, more control)
- **Custom Laravel Pulse integration** — if we want it fully in-product

Recommendation: Use BetterUptime initially (embeds at `/status` via iframe or redirect to `status.flowflex.com`). Migrate to custom later if desired.

### What to Monitor

| Component | Check type | Frequency |
|---|---|---|
| `flowflex.com` (marketing) | HTTP 200 | 1 min |
| `app.flowflex.com` (login) | HTTP 200 | 1 min |
| `api.flowflex.com` (API health) | HTTP 200 on `/api/v1/health` | 1 min |
| Database (read) | TCP port check | 5 min |
| Queue worker | Heartbeat job | 5 min |
| File storage (S3/R2) | Signed URL test | 5 min |
| Email delivery (Mailgun/SES) | Delivery test | 15 min |

### Incident Communication

When an incident occurs:
1. Automated alert to FlowFlex team (PagerDuty or email)
2. Status page updated within 15 minutes
3. If affecting customers: in-app notification banner to all tenants
4. Post-incident report published within 72 hours

Status page incident communication format:
- **Investigating:** "We are aware of issues affecting [component] and are investigating."
- **Identified:** "We have identified the issue: [short description]. A fix is being deployed."
- **Monitoring:** "The fix has been deployed. We are monitoring to confirm resolution."
- **Resolved:** "This incident is resolved. [Duration of impact]. See post-incident report: [link]"

---

## Changelog Page (`/changelog`)

### Purpose

Transparency about product progress. Valuable for:
- Prospects evaluating whether the product is actively maintained
- Existing customers understanding what's new
- SEO (changelog entries often rank for "[feature] update" searches)

### Structure

**Listing page:** Reverse chronological, 10 entries per page
Each entry card: date + type badge + title + 2-line description + `Read more →`

**Individual entry:** Full description + screenshots + related docs link

Managed in Admin Panel. See [[Admin Panel CMS]].

## Related

- [[Homepage]]
- [[Marketing Site Overview]]
- [[Legal & Compliance Pages]]
- [[Admin Panel CMS]]
