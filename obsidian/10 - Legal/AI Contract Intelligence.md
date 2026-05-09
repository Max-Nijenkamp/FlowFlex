---
tags: [flowflex, domain/legal, ai, contracts, phase/6]
domain: Legal
panel: legal
color: "#7C3AED"
status: planned
last_updated: 2026-05-08
---

# AI Contract Intelligence

AI that reads your contracts so you don't have to miss a clause. Upload any contract — supplier agreement, NDA, SLA, employment contract — and AI extracts key dates, obligations, risks, and flags non-standard terms. Replaces expensive legal review for routine contracts.

**Who uses it:** Legal teams, procurement managers, finance (for payment terms), HR (for employment contracts)
**Filament Panel:** `legal`
**Depends on:** Core, [[Contract Management]], [[AI Infrastructure]], [[File Storage]]
**Phase:** 6

---

## Features

### Contract Ingestion

- Upload: PDF, Word (.docx), or drag-and-drop
- Email-to-contract: forward contract email → auto-attached to inbox for review
- API ingestion: contracts from DocuSign, Adobe Sign auto-synced after signature
- Bulk upload: ingest entire contracts folder on onboarding

### AI Extraction

- **Key dates**: effective date, expiry date, renewal deadline, notice period
- **Parties**: counterparty name, registered address, signatory names
- **Financial terms**: contract value, payment schedule, payment terms (Net 30 etc.), late payment penalties, price escalation clauses
- **Obligations**: what each party must do, by when — extracted as structured list
- **Termination triggers**: events that allow early termination
- **Liability cap**: maximum liability amount, indemnification scope
- **IP ownership**: who owns IP created under the contract
- **Governing law and jurisdiction**: which country's law applies
- **Confidentiality**: NDA scope and duration

### Risk Flagging

- Non-standard clauses flagged: uncapped liability, auto-renewal with short notice window, unilateral change rights, non-compete that's overly broad
- Risk score per contract: Low / Medium / High
- Specific risk explanations: "Clause 8.2 contains uncapped indemnification — this is unusual"
- Comparison: deviation from your standard template highlighted

### Contract Template Library

- Upload your standard contract templates
- AI compares incoming contracts against your template
- Deviation report: what does the counterparty's version change from your standard?
- Redline view: side-by-side diff of their version vs your template

### Renewal & Obligation Tracking

- All extracted dates auto-added to renewal calendar
- 90/30/7 day reminders before expiry or notice deadline
- Obligation tracker: each extracted obligation becomes a trackable item (assigned owner, due date, status)
- Finance integration: payment obligation due dates appear in cash flow forecast

### Search & Query

- Full-text search across all contracts
- Natural language query: "which contracts expire before December and have automatic renewal?"
- "Show all contracts where we have uncapped liability"
- Filter by: contract type, party, expiry date range, risk score

---

## Database Tables (3)

### `legal_contract_extractions`
| Column | Type | Notes |
|---|---|---|
| `contract_id` | ulid FK | → contract_management contracts |
| `extracted_at` | timestamp | |
| `model_used` | string | |
| `parties` | json | [{name, role, address}] |
| `effective_date` | date nullable | |
| `expiry_date` | date nullable | |
| `notice_period_days` | integer nullable | |
| `contract_value` | decimal nullable | |
| `payment_terms` | string nullable | |
| `governing_law` | string nullable | |
| `risk_score` | enum | `low`, `medium`, `high` |
| `risk_flags` | json | [{clause, description, severity}] |
| `obligations` | json | [{party, description, due_date}] |

### `legal_contract_obligations`
| Column | Type | Notes |
|---|---|---|
| `contract_id` | ulid FK | |
| `description` | text | |
| `party_responsible` | string | |
| `due_date` | date nullable | |
| `assigned_to_id` | ulid FK nullable | |
| `status` | enum | `pending`, `in_progress`, `completed`, `overdue` |

### `legal_template_comparisons`
| Column | Type | Notes |
|---|---|---|
| `contract_id` | ulid FK | |
| `template_id` | ulid FK | |
| `deviations` | json | [{clause, standard_text, actual_text, severity}] |
| `compared_at` | timestamp | |

---

## Permissions

```
legal.ai-contracts.view
legal.ai-contracts.upload
legal.ai-contracts.view-extractions
legal.ai-contracts.manage-obligations
legal.ai-contracts.view-risk
```

---

## Competitor Comparison

| Feature | FlowFlex | Ironclad | Kira | Luminance |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€€€€) | ❌ (€€€€) | ❌ (€€€€) |
| AI clause extraction | ✅ | ✅ | ✅ | ✅ |
| Risk scoring | ✅ | ✅ | ✅ | ✅ |
| Obligation tracking | ✅ | ✅ | ❌ | partial |
| Finance cash flow integration | ✅ | ❌ | ❌ | ❌ |
| NL/EU law templates | ✅ | ❌ | ❌ | partial |

---

## Related

- [[Legal Overview]]
- [[Contract Management]]
- [[AI Infrastructure]]
- [[Finance Overview]]
- [[Data Privacy]]
