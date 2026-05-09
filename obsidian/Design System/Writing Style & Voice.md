---
tags: [flowflex, design, writing, voice, microcopy]
domain: Design System
status: built
last_updated: 2026-05-08
---

# Writing Style & Voice

How FlowFlex speaks. Direct, calm, confident. Not startup-hype, not enterprise-stiff.

Updated for 2026: AI-generated content guidelines, error message standards (structured format), onboarding copy principles for AI-assisted flows, and permission/consent language.

## The FlowFlex Voice

One sentence: FlowFlex sounds like a knowledgeable colleague who respects your time.

**It is:** Direct. Specific. Calm. Human. Confident without arrogance.
**It is not:** Casual-startup ("Hey there!"). Enterprise-robotic ("The operation could not be completed"). Sycophantic. Over-explaining.

## Tone by Context

| Context | Tone | Example |
|---|---|---|
| Navigation labels | Noun-first, short | "Employees", "Invoices", "Dashboard" |
| Button labels | Verb-first, imperative | "Create Invoice", "Add Employee", "Send" |
| Empty states | Helpful, action-oriented | "No invoices yet — create your first" |
| Error messages | Specific, constructive | "Invoice total must be > £0 — add a line item" |
| Success messages | Brief, no fanfare | "Invoice sent", "Employee added" |
| Confirmation dialogs | Direct, consequence-clear | "Delete invoice #INV-0047? This cannot be undone." |
| Tooltips | One sentence, no period | "Shows the total amount billed this month" |
| Onboarding | Warm, guiding, not patronising | "Let's get your team set up" |
| AI responses | Calm, factual, attributed | "Based on your data: revenue grew 18% last quarter" |
| Permissions / consent | Clear, specific, no legalese | "FlowFlex will access your Google Calendar to sync meetings" |
| Destructive confirmations | Named item + consequence | "Delete Jane Smith's employee record?" not "Are you sure?" |

## Microcopy Rules

**General**
- **Button labels** are always title case with a verb: "Create Invoice", "Save Changes", "Delete Employee"
- **Navigation items** are title case nouns: "Time Tracking", "Sales Pipeline", "Knowledge Base"
- **Placeholder text** gives an example, not an instruction: `e.g. Jane Smith` not `Enter name`
- **Never say "please"** — adds length without warmth
- **Never say "oops"** — not serious enough for a business tool
- **Never say "something went wrong"** without specifics and next steps
- **Use "you" not "the user"** — speak directly to the person
- **Never use passive voice** for errors: "Invoice was not saved" → "Your invoice was not saved — [reason]"

**AI-related microcopy**
- Never say "AI thinks…" or "AI believes…" — AI doesn't think or believe. Say "Based on your data:" or "Suggested:"
- Never say "AI is working" — say what it's specifically doing: "Analysing this quarter's pipeline…"
- Never attribute certainty AI doesn't have: "You'll probably want to…" not "You need to…"
- Always offer a "Dismiss" or "Not now" path — never trap the user in an AI interaction
- When AI generates content: label it "AI draft" — don't silently inject AI text without disclosure

## Error Message Standard

All error messages follow this structure: **[What happened] — [Why] — [What to do]**

| Component | Format | Example |
|---|---|---|
| What happened | Past tense, specific | "Invoice not saved" |
| Why | Concise reason | "The total is £0" |
| What to do | Actionable next step | "Add at least one line item" |

**Combined:** "Invoice not saved — the total is £0. Add at least one line item to continue."

**Validation errors:**
- Show inline, below the field that caused the error
- Never "Error: invalid input" — always say what is invalid and why
- For multi-field forms with multiple errors: show all errors at top summary AND inline

**Network/server errors:**
- Always include an error reference or code: "Could not connect — please check your internet connection. If this keeps happening, contact support (ref: ERR-502)."
- Provide a retry action when applicable

**Permission errors:**
- "You don't have permission to delete employees — contact your admin to request access."
- Never just "Access denied" or "403 Forbidden"

## AI-Generated Content Guidelines

When AI generates text that appears in the product, these rules apply:

### Transparency Rules

- All AI-drafted content must carry an "AI draft" badge (tide-100 bg, tide-600 text)
- The badge must remain visible until the user edits or explicitly accepts the content
- Accepting AI content is a deliberate action — not auto-accepted on save

### AI Writing Voice

The AI assistant in FlowFlex adopts the FlowFlex voice — not a generic LLM voice. It must:

- Be concise: no preamble ("Certainly! Here's a draft…"), no sign-off ("I hope this helps!")
- Be specific: reference actual data from the user's workspace
- Be honest about uncertainty: "I don't have data on this — check your finance module" not a fabricated answer
- Use the same formatting standards (currency, dates, numbers) as the rest of the platform

### AI Instruction Copy (UI prompts)

When prompting users to use AI features:

- "Ask AI to draft this proposal" — not "Let AI write this for you!" (too passive)
- "Get a summary of this employee's activity" — not "AI can summarise!"
- Keep prompts in the same tone as the rest of the UI — not special "AI-excitement" language

## Permission & Consent Language

When requesting user permissions (OAuth, browser permissions, data access):

- Name exactly what access is being requested: "Calendar read access"
- Explain why: "To sync meetings with your FlowFlex schedule"
- State duration: "You can revoke this access at any time in Settings"
- Never use vague requests: "Access your account" — always specify "Read your Google Calendar events"

For data processing consent (GDPR-relevant features):
- Plain language only — no legalese in UI copy
- Legal text lives in the Privacy Policy — the UI just needs to be honest and clear

## Number & Date Formatting

| Type | Format | Example |
|---|---|---|
| Currency (default) | Symbol + 2dp + thousands separator | £1,234.50 |
| Currency (large) | Abbreviated with symbol | £1.2M, £45k |
| Large numbers | Abbreviated at 10k+ | 12.4k, 1.2M |
| Dates (full) | Day Month Year | 14 March 2026 |
| Dates (short) | DD MMM YYYY | 14 Mar 2026 |
| Dates (compact) | Locale-aware | 14/03/2026 (EU) · 03/14/2026 (US) |
| Time | 24h default, locale-aware | 14:32 |
| Relative time (< 24h) | "X minutes ago", "2 hours ago" | "3 minutes ago" |
| Relative time (1–6 days) | Day name | "Yesterday", "Monday" |
| Relative time (> 6 days) | Full date | "14 Mar 2026" |
| Percentages | 1dp unless whole number | 12.5%, 50% |
| Duration | Abbreviated | 2h 30m |
| File sizes | 1dp except < 1MB | 4.2 MB, 340 KB |
| AI confidence indicators | Percentage or descriptor | "High confidence" / "Low confidence" — never show raw probability numbers to users |

## Empty State Copy Pattern

Structure: [Icon] + [Heading] + [Subtext] + [CTA] + [Optional AI suggestion]

| Element | Style | Length |
|---|---|---|
| Heading | `text-h4`, `slate-800` | 2–5 words |
| Subtext | `text-body`, `slate-500` | 1 sentence, max 60 chars |
| CTA | Primary button, verb-first | 2–3 words |
| AI suggestion (optional) | ocean-50 chip, "AI can help…" | 1 sentence |

**Examples:**

- Employees: "No employees yet" / "Add your first team member to get started." / [Add Employee]
- Invoices: "No invoices sent" / "Create your first invoice and start getting paid faster." / [Create Invoice]
- Tasks: "No tasks here" / "Start a new project or add your first task." / [Add Task]
- AI suggestion variant: "No pipeline data" / "Add your first deal to start tracking revenue." / [Add Deal] + "AI can analyse your pipeline once you have 5+ deals."

Never write empty state copy that makes the user feel like they've done something wrong. The product is empty because they're new — that's fine.

## Onboarding Copy Principles

- Never start with a feature tour — start with the user's goal: "What do you want to set up first?"
- Steps are numbered: "1 of 4 — Add your team"
- Progress is celebrated briefly: "Your team is set up." — then immediately move to the next step. No confetti. No "Woohoo!".
- AI-assisted setup: when AI pre-fills something from connected data (e.g., org chart from Google Workspace), always say where it came from: "We found 12 employees in your Google Workspace — you can review and edit these below."

## Related

- [[Brand Foundation]]
- [[Component Library]]
- [[AI & Conversational UI]]
