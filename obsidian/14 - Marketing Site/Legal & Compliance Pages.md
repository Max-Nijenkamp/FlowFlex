---
tags: [flowflex, legal, privacy, gdpr, terms, cookies]
domain: Marketing Site
status: planned
last_updated: 2026-05-07
---

# Legal & Compliance Pages

All legal documents that FlowFlex must publish. These protect the company, protect customers, and are required for GDPR compliance and B2B sales. **Have all documents reviewed by a qualified lawyer before publishing.**

## Document Inventory

| Document | URL | Audience | Legally Required |
|---|---|---|---|
| Privacy Policy | `/legal/privacy` | All visitors + customers | Yes (GDPR Art. 13/14) |
| Terms of Service | `/legal/terms` | Customers | Yes (contract) |
| Cookie Policy | `/legal/cookies` | All visitors | Yes (ePrivacy Directive) |
| Data Processing Agreement | `/legal/dpa` | Business customers | Yes (GDPR Art. 28) |
| Acceptable Use Policy | `/legal/aup` | Customers | Strongly recommended |
| Security Policy | `/security` | Customers + prospects | Best practice (not legal req) |

---

## Privacy Policy (`/legal/privacy`)

### What It Must Cover (GDPR Articles 13 & 14)

1. **Data controller identity:** FlowFlex Ltd, [address], [company number], contact: privacy@flowflex.com
2. **What data we collect:**
   - Visitors: IP address, browser/device info, pages visited, cookies
   - Demo requests: name, email, company name, company size, phone (optional)
   - Newsletter subscribers: email address
   - Customers (tenants): company data, employee data (as data processor — their data, our platform)
3. **Why we collect it (legal basis per GDPR Art. 6):**
   - Website analytics: Legitimate interest (Art. 6(1)(f))
   - Demo request: Contract pre-performance (Art. 6(1)(b)) + Consent
   - Marketing emails: Consent (Art. 6(1)(a))
   - Customer data processing: Contract (Art. 6(1)(b)) + DPA (Art. 28)
4. **How long we keep it:**
   - Demo request data: 2 years after last interaction
   - Customer data: Duration of contract + 30 days after cancellation
   - Audit logs: As specified in customer's plan
   - Marketing data: Until consent is withdrawn
5. **Who we share data with:**
   - Stripe (payment processing)
   - AWS/Cloudflare R2 (file storage — EU region only)
   - Google Analytics (anonymised, if consent given)
   - Any other sub-processors listed in the DPA
6. **International transfers:** Data stays in EU. If any processor operates outside EU, list adequacy decision or safeguard mechanism.
7. **Your rights (GDPR Art. 15–22):**
   - Right to access
   - Right to rectification
   - Right to erasure ("right to be forgotten")
   - Right to restriction of processing
   - Right to data portability
   - Right to object
   - Rights related to automated decision-making
   - How to exercise: email privacy@flowflex.com
8. **Right to complain:** Dutch DPA (Autoriteit Persoonsgegevens) or relevant national authority
9. **Cookie information:** Brief summary, links to full Cookie Policy
10. **Last updated date** — always visible at top

### Template Language (Key Sections)

**Who we are:**
> FlowFlex is a modular business platform. The data controller for all personal data collected on flowflex.com is FlowFlex Ltd, [address]. Contact: privacy@flowflex.com.

**Note on customer data:**
> When you use FlowFlex as a customer, you may store personal data about your own employees and customers within the platform. In this capacity, FlowFlex acts as a **data processor** on your behalf, and you are the **data controller** for that data. Our processing activities are governed by our Data Processing Agreement. Your obligations as a data controller under GDPR are separate from our obligations as your processor.

---

## Terms of Service (`/legal/terms`)

### Sections Required

1. **Acceptance** — By using FlowFlex you agree to these terms
2. **Definitions** — "Platform", "Workspace", "Tenant", "Module", "User", "Content"
3. **Account creation** — How accounts are created (admin-created, no self-registration); age requirement (18+)
4. **Subscription and billing:**
   - Plan tiers and pricing (reference pricing page, not hardcode)
   - Billing cycles (monthly/annual)
   - Auto-renewal
   - Upgrade/downgrade/cancellation rules
   - Refunds: pro-rata on annual plans cancelled within 30 days, no refund after that
   - Failed payment: 7-day grace period, then suspension; 14 days suspension before deletion
5. **Acceptable use** — Reference the AUP; key prohibitions inline
6. **Data ownership:**
   - Customer data remains the customer's property
   - FlowFlex does not sell customer data
   - FlowFlex may use aggregated, anonymised usage data for product improvement
7. **Service availability:**
   - We aim for 99.9% uptime
   - Planned maintenance with 24h notice
   - No SLA guarantee on Starter/Pro (Enterprise gets SLA)
8. **Intellectual property:**
   - FlowFlex name, logo, platform code = FlowFlex IP
   - Customer content = customer IP
   - Customer grants FlowFlex a limited licence to process their content to deliver the service
9. **Termination:**
   - Customer may cancel any time
   - FlowFlex may terminate for AUP violation, non-payment, or at our discretion with 30-day notice
   - Data export available for 30 days after termination
10. **Limitation of liability** — Cap at 12 months of fees paid; no consequential damages (check enforceability in applicable jurisdiction)
11. **Indemnification** — Customer indemnifies FlowFlex for claims arising from their content or misuse
12. **Governing law** — Netherlands law; Amsterdam courts (adjust per company registration)
13. **Changes to terms** — 30-day notice for material changes; continued use = acceptance
14. **Contact** — legal@flowflex.com

---

## Cookie Policy (`/legal/cookies`)

### What to Cover

1. **What are cookies** (brief, plain English)
2. **Types we use:**

| Category | Name | Purpose | Duration | Consent Required |
|---|---|---|---|---|
| Strictly Necessary | `flowflex_session` | Session management | Session | No |
| Strictly Necessary | `XSRF-TOKEN` | CSRF protection | Session | No |
| Analytics | `_ga`, `_ga_*` | Google Analytics | 2 years | Yes |
| Analytics | `_hjid` | Hotjar session ID | 1 year | Yes |
| Marketing | `_fbp` | Facebook Pixel | 3 months | Yes |
| Marketing | `li_fat_id` | LinkedIn Insight | 1 month | Yes |
| Marketing | `_uetsid` | Bing UET | 1 day | Yes |
| Preferences | `ff_cookie_consent` | Stores consent | 1 year | No (stores the choice) |

3. **How to manage cookies:**
   - "Cookie preferences" link in footer opens consent modal
   - Browser settings
   - Opt-out links (Google Analytics opt-out, etc.)
4. **Changes to this policy** — same as Terms

### Cookie Consent Modal

Appears on first visit (not returning if consent given).

**Appearance:** Bottom banner or centre modal. Design follows brand design system.

**Options:**
- "Accept all" — enables analytics + marketing cookies
- "Reject non-essential" — strictly necessary only
- "Manage preferences" → expands to category toggles (Necessary ON/locked, Analytics toggle, Marketing toggle)

**Implementation:** First-party JavaScript, no external CookieBot/OneTrust. Consent stored in `ff_cookie_consent` cookie as JSON:
```json
{"necessary": true, "analytics": false, "marketing": false, "timestamp": "2026-05-07T..."}
```

GTM configured to fire Analytics/Marketing tags only when consent for that category is `true`.

---

## Data Processing Agreement (`/legal/dpa`)

Required under GDPR Article 28 for every B2B customer. Must be accepted as part of signup.

### What It Covers

1. **Subject matter and duration** — FlowFlex processes data on behalf of the customer for the duration of the subscription
2. **Nature and purpose** — Providing the FlowFlex platform; no processing for FlowFlex's own purposes
3. **Type of personal data** — Whatever the customer chooses to store (employee data, customer data, etc.)
4. **Categories of data subjects** — Customer's employees, their clients, etc. (customer-determined)
5. **Processor obligations (Art. 28(3)):**
   - Process only on documented instructions
   - Ensure confidentiality of all authorised personnel
   - Implement appropriate technical and organisational security measures (Art. 32)
   - Engage sub-processors only with prior written consent
   - Assist with data subject rights requests
   - Assist with security obligations and breach notification
   - Delete or return all data at end of contract
   - Provide all information necessary to demonstrate compliance
6. **Sub-processors list:** (must be kept up to date — link to live list on website)
   - AWS (storage, EU region)
   - Stripe (payment data only — not customer platform data)
   - [others as added]
7. **Security measures (technical and organisational):**
   - Encryption at rest (AES-256) and in transit (TLS 1.2+)
   - Access controls (RBAC, 2FA for admin)
   - Audit logging
   - Regular security testing
   - Incident response procedures
8. **Data breach notification:** Within 72 hours of becoming aware of a breach that affects customer data
9. **Data transfers:** Primary storage EU. No transfer outside EEA without adequacy decision or Standard Contractual Clauses (SCCs)
10. **Governing law:** Same as Terms of Service

### How to Accept the DPA

Option A: Inline in Terms of Service — "By accepting these Terms, you also accept our DPA."
Option B: Separate acceptance flow in workspace settings — "Download DPA" + "I accept" button.

Recommendation: Option A for Starter/Pro, Option B (countersigned) for Enterprise customers.

---

## Acceptable Use Policy (`/legal/aup`)

### Prohibited Uses

Customers may not use FlowFlex to:

1. Process unlawful content or conduct unlawful activities
2. Transmit spam, phishing, or unsolicited commercial messages
3. Upload malware, viruses, or malicious code
4. Attempt to gain unauthorised access to other tenants' data
5. Use the platform to mine cryptocurrencies
6. Reverse-engineer, decompile, or attempt to extract platform source code
7. Resell or white-label the platform without written agreement
8. Store or process data in violation of applicable laws
9. Abuse the API in ways that degrade service for other customers
10. Impersonate FlowFlex or its staff
11. Use FlowFlex in violation of any applicable sanctions regimes

### Consequences of Violation

- Warning and required remediation
- Immediate suspension of account (for serious violations)
- Termination without refund

### Reporting

If you believe another user is violating this policy: abuse@flowflex.com

---

## Security Page (`/security`)

Not a legal document but expected by B2B buyers. Link from footer.

### Sections

1. **Data storage:** "Your data is stored in EU data centres (AWS Frankfurt). It never leaves the EU unless you connect a third-party integration that does so."

2. **Encryption:** "All data is encrypted at rest using AES-256 and in transit using TLS 1.3. Database backups are encrypted and stored separately."

3. **Access controls:**
   - Two-factor authentication enforced for all platform admin accounts
   - Role-based access control at every level
   - Tenant data is isolated — no cross-tenant data access
   - FlowFlex staff can only access customer data via monitored impersonation (with your consent)

4. **Penetration testing:** "We conduct annual penetration tests by independent third parties. Results are available under NDA for Enterprise customers."

5. **Compliance:**
   - GDPR compliant (EU data residency, DPA available)
   - SOC 2 Type II: in progress (target: Q[X] 2026)
   - ISO 27001: roadmap (Phase 6)

6. **Vulnerability disclosure:** "Found a security issue? Email security@flowflex.com. We will respond within 48 hours. We do not take legal action against good-faith security researchers."

7. **Incident response:** "In the event of a data breach that affects your data, we will notify you within 72 hours in accordance with GDPR requirements."

8. **Uptime and backups:** "We maintain daily encrypted backups with 30-day retention. See real-time uptime at /status."

---

## Legal Pages — Shared Design Requirements

- All legal pages: `slate-100` background, max-width 800px content area
- H1 = document title, H2 = major sections
- Last updated date prominently at top
- Print-friendly (link to printable version or just `@media print` styles)
- No CTAs — these are compliance documents, not sales pages
- PDF download available (auto-generated) for Privacy Policy, Terms, DPA

## Versioning

All legal documents are versioned. When a document changes:
1. Update the "Last updated" date
2. Archive the previous version at `/legal/{document}/archive/{date}`
3. Email all customers 30 days in advance for material changes
4. For minor updates (typos, clarifications), no notice needed

## Related

- [[Demo Request Flow]]
- [[Marketing Site Overview]]
- [[Security Rules]]
