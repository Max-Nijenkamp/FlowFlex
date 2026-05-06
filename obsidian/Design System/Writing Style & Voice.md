---
tags: [flowflex, design, writing, voice, microcopy]
domain: Design System
status: built
last_updated: 2026-05-06
---

# Writing Style & Voice

How FlowFlex speaks. Direct, calm, confident. Not startup-hype, not enterprise-stiff.

## Tone by Context

| Context | Tone |
|---|---|
| Navigation labels | Noun-first, short ("Employees", "Invoices", "Dashboard") |
| Button labels | Verb-first, imperative ("Create Invoice", "Add Employee", "Send") |
| Empty states | Helpful, friendly, action-oriented |
| Error messages | Specific, calm, tell the user what to do |
| Success messages | Brief confirmation, no over-celebrating |
| Confirmation dialogs | Direct, make the consequence clear |
| Tooltips | One sentence, no period at end |
| Onboarding | Warm, guiding, not patronising |

## Microcopy Rules

- **Button labels** are always title case with a verb: "Create Invoice", "Save Changes", "Delete Employee"
- **Navigation items** are title case nouns: "Time Tracking", "Sales Pipeline", "Knowledge Base"
- **Placeholder text** gives an example, not an instruction: `e.g. Jane Smith` not `Enter name`
- **Error messages** always say what went wrong AND what to do: "Invoice total must be greater than £0 — add at least one line item."
- **Confirmation dialogs** name the item being deleted: "Delete invoice #INV-0047?" not "Are you sure?"
- **Never say "please"** in UI text — it adds length without warmth
- **Never say "oops"** — not serious enough for a business tool
- **Never say "something went wrong"** without a code or suggestion
- **Use "you" not "the user"** — speak directly to the person

## Number & Date Formatting

| Type | Format | Example |
|---|---|---|
| Currency | Symbol + 2dp + thousands separator | £1,234.50 |
| Large numbers | Abbreviate at 1k+ | 12.4k, 1.2M |
| Dates (full) | Day Month Year | 14 March 2025 |
| Dates (short) | DD MMM YYYY | 14 Mar 2025 |
| Dates (compact) | DD/MM/YYYY or MM/DD/YYYY (locale-aware) | 14/03/2025 |
| Time | 24h by default, locale-aware | 14:32 |
| Relative time | For recent events | "3 minutes ago", "Yesterday" |
| Percentages | 1dp unless whole number | 12.5%, 50% |
| Duration | Abbreviate | 2h 30m |

## Empty State Copy Pattern

Structure: [Icon] + [Heading] + [Subtext] + [CTA]

Examples:
- "No employees yet" / "Add your first team member to get started" / [Add Employee]
- "No invoices sent" / "Create your first invoice and start getting paid faster" / [Create Invoice]
- "No tasks here" / "Start a new project or add your first task" / [Add Task]

## Related

- [[Brand Foundation]]
- [[Component Library]]
