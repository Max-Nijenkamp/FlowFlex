---
tags: [flowflex, marketing, pricing, plans, billing]
domain: Marketing Site
status: planned
last_updated: 2026-05-07
---

# Pricing Page

The pricing page is a decision-stage page. Visitors here have already decided they want the product — they're deciding which plan. The page must remove friction and objections, not create them.

## URL

`https://flowflex.com/pricing`

## Meta

```
<title>Pricing — FlowFlex</title>
<meta name="description" content="Three plans for every business size. Starter from €X/month. Pro from €X/month. Enterprise custom pricing. Activate only the modules you need.">
```

## Page Structure

### 1. Hero

**Headline:** "Simple pricing. Only pay for what you use."
**Subtext:** "Three plans. Activate modules à la carte. Upgrade or downgrade any time."

**Annual/Monthly toggle:**
- Default: Monthly
- Annual: "Save 2 months (17% discount)"
- Toggle updates prices on page using Alpine.js — no page reload
- Annual prices shown as per-month equivalent (e.g. "€X/mo, billed annually")

---

### 2. Plan Cards

Three cards side by side (Pro highlighted with `ocean-500` border and "Most Popular" badge).

---

#### Starter

**Price:** €X / month (billed monthly) · €X / month (billed annually)
**Users:** Up to 10
**Modules:** Up to 5 active
**Storage:** 5 GB
**Support:** Standard (email, 48h response)

**Included:**
- Core Platform (always active)
- Any 5 modules of your choice
- Role-based access control
- In-app and email notifications
- REST API access (read-only)
- Activity audit log (30-day retention)

**CTA:** `Start free trial →` → goes to `/demo` (no self-registration)

**Note below CTA:** "14-day free trial, all Pro features included"

---

#### Pro

**Badge:** Most Popular
**Price:** €X / month (billed monthly) · €X / month (billed annually)
**Users:** Up to 100
**Modules:** Unlimited
**Storage:** 100 GB
**Support:** Priority (email + live chat, 8h response)

**Everything in Starter, plus:**
- Unlimited modules
- Full REST API access (read + write + webhooks)
- Advanced permissions and custom roles
- Activity audit log (1-year retention)
- Custom branding (logo + colours on workspace)
- Module preview mode (explore before activating)
- Annual discount toggle
- Overage notifications and soft limits

**CTA:** `Start free trial →`

**Note below CTA:** "14-day free trial · No credit card required"

---

#### Enterprise

**Price:** Custom pricing
**Users:** Unlimited
**Modules:** Unlimited
**Storage:** Unlimited
**Support:** Dedicated account manager + SLA

**Everything in Pro, plus:**
- Unlimited users and storage
- Custom SLA (uptime guarantees, response times)
- Dedicated account manager
- SSO (SAML 2.0) + SCIM provisioning
- Custom contracts and invoicing
- Audit log unlimited retention
- Advanced security controls (IP allowlist, 2FA enforcement)
- Priority onboarding and training
- API rate limit increases
- Data export to warehouse (BigQuery, Snowflake) — Phase 6

**CTA:** `Talk to sales →` → goes to `/demo?plan=enterprise`

---

### 3. Feature Comparison Table

Full side-by-side table. Toggle to expand/collapse by category.

**Categories:**
- Users & Storage
- Modules
- API & Integrations
- Security & Compliance
- Support
- Billing & Admin

Each row: feature name + Starter cell + Pro cell + Enterprise cell.
Cells use: ✅ = included · ❌ = not included · Text = limit or description.

---

### 4. Module Pricing Explainer

**Headline:** "How module pricing works"

**Explainer (3 cards):**
1. **You choose your modules** — Browse 99+ modules. Activate the ones your business needs. Deactivate any time.
2. **Pay per module (Starter)** — Starter plan includes 5 modules. Add extras at €X/module/month. Pro and Enterprise: unlimited.
3. **Data stays when you pause** — Deactivate a module and your data is preserved. Reactivate it in seconds.

---

### 5. FAQ Section

Accordion (Alpine.js). Questions managed in Admin Panel CMS.

Default questions:

**Q: Can I switch plans later?**
Yes. Upgrade or downgrade any time from your workspace settings. Upgrades take effect immediately with prorated billing. Downgrades take effect at the next billing cycle.

**Q: What counts as a user?**
A user is any person with access to your FlowFlex workspace. Inactive users (archived employees) don't count toward your limit.

**Q: Is there a free trial?**
Every new workspace starts with a 14-day trial that includes all Pro features. No credit card required. After 14 days you'll be prompted to choose a plan.

**Q: Can I try a module before activating it?**
Yes, on Pro and Enterprise plans. Module preview mode lets you explore the UI with read-only demo data before activating.

**Q: What happens to my data if I cancel?**
Your data is retained for 30 days after cancellation. You can export everything as CSV or JSON. After 30 days, data is permanently deleted.

**Q: Do you offer discounts for non-profits or startups?**
Yes. Contact us at billing@flowflex.com — we offer 50% off for registered non-profits and early-stage startups (< 2 years, < 10 employees).

**Q: Is there a module-level pricing breakdown?**
Starter plan: core 5 modules included, extra modules €X/month each. Pro and Enterprise: all modules included. Specific per-module pricing is available in your workspace billing settings.

**Q: What payment methods do you accept?**
Credit and debit cards (Visa, Mastercard, Amex). SEPA Direct Debit for EU customers. Enterprise: invoice/bank transfer available.

**Q: Where is my data stored?**
Primary data centres: EU (Netherlands/Germany). No data leaves the EU unless you explicitly enable a third-party integration that does so. See our [[Data Processing Agreement]].

**Q: Do you charge per module per user?**
No. Module pricing is per workspace (company), not per user. All users within your workspace can access any active module (subject to their role permissions).

---

### 6. Trust Signals Bar

Below FAQ:
- `GDPR compliant` — EU data residency
- `SOC 2` — In progress (show when certified)
- `99.9% uptime` — linked to `/status`
- `Stripe-secured billing` — Stripe logo

---

### 7. CTA Footer

Same as homepage final CTA section:
- "Still not sure? Talk to us first."
- "Book a 30-minute demo. No commitment."
- `[Request a demo →]`

## Pricing Data Management

Prices are **not hardcoded** in Blade templates. They are stored in:
- `config/pricing.php` — plan limits, base prices
- Admin panel `/admin/pricing` — marketing-facing display prices, feature table content

This allows prices to be updated without a code deploy.

## Structured Data (JSON-LD)

Add `Product` schema for each plan on this page so Google can show pricing in rich results.

```json
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "FlowFlex",
  "applicationCategory": "BusinessApplication",
  "offers": [
    {
      "@type": "Offer",
      "name": "Starter",
      "price": "X",
      "priceCurrency": "EUR",
      "priceSpecification": {
        "@type": "UnitPriceSpecification",
        "billingDuration": "P1M"
      }
    }
  ]
}
```

## Related

- [[Homepage]]
- [[Demo Request Flow]]
- [[Module Billing Engine]]
- [[Admin Panel CMS]]
