---
type: module
domain: Legal & Compliance
panel: legal
cssclasses: domain-legal
phase: 7
status: complete
migration_range: 570000–574999
last_updated: 2026-05-12
---

# AI Contract Intelligence

LLM-powered contract analysis — clause extraction, risk scoring against an approved clause library, obligation identification, and automated redline suggestions. Reduces lawyer review time by 60–80% for standard contracts.

**Panel:** `legal`  
**Phase:** 7  
**Migration range:** `570000–574999`

---

## Features

### Core (MVP)

- Contract upload: PDF / DOCX → text extraction
- Clause extraction: identify and label key clauses (payment terms, termination, liability cap, IP assignment, confidentiality, governing law)
- Risk scoring: compare extracted clauses against approved clause library — flag deviations
- Obligation extraction: identify obligations with party, action, and deadline
- Plain-language summary: AI generates executive summary of contract in simple English

### Advanced

- Redline suggestions: AI suggests alternative clause wording from approved library
- Benchmark comparison: compare terms against market standard or internal playbook
- Counterparty risk flag: unusual or one-sided terms highlighted for lawyer review
- Clause version history: track how a clause evolved across negotiation drafts
- Batch analysis: upload 10+ contracts, get comparative risk report

### AI-Powered

- Claude-powered analysis: `claude-opus-4-7` with contract text as context
- Clause classifier: fine-tuned classifier for 30+ standard clause types
- Risk taxonomy: 5-level risk classification (low → critical) per clause
- Multi-document comparison: "compare this NDA against our standard NDA"

---

## Data Model

```erDiagram
    contract_analyses {
        ulid id PK
        ulid contract_id FK
        string status
        json extracted_clauses
        decimal overall_risk_score
        json obligations
        text ai_summary
        timestamp analysed_at
    }

    clause_library {
        ulid id PK
        ulid company_id FK
        string clause_type
        string risk_level
        text approved_wording
        text description
        boolean is_fallback_acceptable
    }
```

---

## Events

### Emitted

| Event | When | Consumed By |
|---|---|---|
| `ContractAnalysisComplete` | AI finishes analysis | Notifications (contract owner), Legal (show risk report) |
| `HighRiskClauseDetected` | Risk score above threshold | Notifications (legal team escalation) |

### Consumed

| Event | From | Action |
|---|---|---|
| `ContractExecuted` | Contract Management | Trigger obligation extraction and tracking setup |

---

## Permissions

```
legal.ai-analysis.run
legal.ai-analysis.view-results
legal.clause-library.manage
legal.ai-analysis.view-any
```

---

## Related

- [[MOC_Legal]]
- [[contract-management]] — AI analysis runs on contracts stored here
- [[MOC_DMS]] — contract documents sourced from document store
