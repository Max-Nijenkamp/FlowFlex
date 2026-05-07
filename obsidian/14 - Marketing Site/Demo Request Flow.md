---
tags: [flowflex, marketing, demo, lead-capture, conversion]
domain: Marketing Site
status: planned
last_updated: 2026-05-07
---

# Demo Request Flow

The primary conversion action on the marketing site. No self-registration — every new customer starts with a demo request. This flow must feel lightweight and trustworthy.

## Why No Self-Service Signup

FlowFlex does not offer self-registration at launch. Reasons:
1. Platform requires careful onboarding (module selection, initial setup)
2. Quality of leads matters more than volume at this stage
3. Sales touchpoint lets us qualify fit and prevent churn from mismatched use cases
4. Avoids spam/abuse accounts before billing is implemented (Phase 6)

*Review this decision at 100+ customers — consider a freemium or self-serve tier.*

## URL

`https://flowflex.com/demo`

All `/register`, `/signup`, `/trial` routes redirect here.

## Meta

```
<title>Request a Demo — FlowFlex</title>
<meta name="description" content="Book a 30-minute demo with the FlowFlex team. We'll walk you through the modules that replace your current stack.">
```

---

## Demo Request Form

### Form Fields

| Field | Type | Required | Notes |
|---|---|---|---|
| First name | Text | Yes | |
| Last name | Text | Yes | |
| Work email | Email | Yes | Block free providers (gmail, hotmail, etc.) with a soft warning — not hard block |
| Company name | Text | Yes | |
| Company size | Select | Yes | 1–10 · 11–50 · 51–200 · 201–500 · 500+ |
| Which modules interest you most? | Multi-checkbox | No | Shows all 13 domain names as checkboxes |
| How did you hear about us? | Select | No | Google · LinkedIn · Word of mouth · Blog/article · Comparison site · Other |
| Anything you'd like us to know? | Textarea | No | Optional free-text, max 500 chars |
| Phone number | Tel | No | Optional — used if email bounces |

### Form Behaviour

- Inline validation on blur (not on submit)
- Error messages follow [[Writing Style & Voice]] patterns ("Email must be a work address — free email providers aren't supported")
- Submit button text: "Request your demo →"
- Disabled on submit until all required fields valid
- On submit: show loading state → success state (no page reload)
- CSRF token always sent
- Honeypot field (hidden, must be empty) for basic bot filtering
- Google reCAPTCHA v3 (invisible, fires on submit — score threshold 0.5)

### Privacy Consent

Below the submit button:
```
By submitting this form, you agree to our Privacy Policy and consent
to FlowFlex contacting you about your request.
```

"Privacy Policy" links to `/legal/privacy`. Required before submission. Use a checkbox: "I agree to be contacted about my request" — pre-checked is not allowed under GDPR. The checkbox must be explicitly ticked.

---

## Thank You / Confirmation State

After successful submission, replace the form with:

```
Heading: We've got your request.
Body:    Expect an email from us within 24 hours to schedule your demo.
         In the meantime, feel free to explore the modules below.
Icon:    Animated checkmark (success-500 colour)
Links:   → Explore all modules   → Read our blog
```

Also send confirmation email (see below).

---

## Confirmation Email

**From:** `demo@flowflex.com`
**Reply-to:** `max@flowflex.com` (or sales@flowflex.com once a team exists)
**Subject:** "We've got your FlowFlex demo request, {first_name}"

**Body:**
```
Hi {first_name},

Thanks for reaching out — we've received your demo request for {company_name}.

Someone from the FlowFlex team will email you within 24 hours to schedule your
30-minute walkthrough. We'll focus on the modules you're most interested in, so
the time is genuinely useful for you.

While you wait, here's what other businesses are using FlowFlex to replace:
→ BambooHR — with our HR & People module
→ Jira/Monday — with Projects & Work
→ Xero/QuickBooks — with Finance
→ HubSpot/Salesforce — with CRM & Sales

Full module list: https://flowflex.com/features

See you soon,
Max
Founder, FlowFlex

P.S. If you have questions before your demo, reply to this email — it goes straight to me.
```

---

## Lead Handling (Admin Panel)

All demo requests land in the Admin Panel at `/admin/demo-requests`.

### DemoRequest Model

Fields stored:
- `id` (ULID)
- `first_name`, `last_name`
- `email`
- `company_name`
- `company_size`
- `modules_interested` (JSON array)
- `heard_from`
- `notes`
- `phone`
- `ip_address`
- `user_agent`
- `utm_source`, `utm_medium`, `utm_campaign`, `utm_content`, `utm_term` (from URL params)
- `status`: `new` · `contacted` · `demo_scheduled` · `demo_done` · `converted` · `lost`
- `assigned_to` (nullable — FlowFlex team member)
- `scheduled_at` (nullable — when demo is booked)
- `notes_internal` (text — internal notes from sales)
- `created_at`, `updated_at`, `deleted_at`

### Admin Panel View

- Table view: sorted by `created_at` desc
- Filters: status, company_size, modules_interested, assigned_to, date range
- Row actions: Mark contacted · Schedule demo · Convert to tenant · Mark lost
- "Convert to tenant" action → opens create-tenant wizard in admin panel, pre-fills company name and email
- Email thread visible if integrated with shared inbox (Phase 3 CRM)

### Notifications

- When new demo request arrives: notify FlowFlex team via email + in-app notification
- When demo is scheduled: confirmation email to prospect with calendar invite attachment (.ics)
- When demo is 24h away: reminder email to prospect
- When 48h passes with status `new` and no action: escalation notification to team

---

## Funnel Tracking

UTM parameters are captured and stored on every demo request. This enables attribution reporting:
- Which ad campaigns drive demo requests
- Which blog posts drive demo requests
- Which module pages convert

GA4 events:
- `demo_form_viewed` — user lands on /demo
- `demo_form_started` — user interacts with first field
- `demo_form_submitted` — successful submission

---

## Quality Score (Future)

Once enough data exists, add a lead scoring model:
- Company size > 50: +2 points
- Multiple modules selected: +1 per module beyond 2
- Work email domain from known company: +1
- Came from comparison page: +2
- utm_medium = paid: note (not score — might indicate lower intent)

Score shown as badge in admin panel. Used to prioritise outreach.

---

## Related

- [[Marketing Site Overview]]
- [[Homepage]]
- [[Admin Panel CMS]]
- [[Legal & Compliance Pages]]
- [[SEM & Paid Advertising]]
