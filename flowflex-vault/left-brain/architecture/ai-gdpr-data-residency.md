---
type: architecture-note
section: architecture
status: decision-required
last_updated: 2026-05-09
---

# AI & GDPR / Data Residency

LLM inference, AI features, and GDPR obligations intersect in non-obvious ways. This note documents the decisions needed before any AI feature ships to EU customers.

---

## The Risk

FlowFlex AI features (Copilot, document processing, meeting intelligence, customer service bot) by definition process personal data:
- Employee names, emails, performance data → HR AI
- Customer contact details, deal notes → CRM AI
- Meeting transcripts with named participants → Meeting Intelligence
- Invoice content, vendor names → Document Processing

Sending this data to US-hosted LLMs (OpenAI GPT-4, Anthropic Claude) without appropriate safeguards is:
- A GDPR Chapter V violation (international transfer without adequacy decision or SCCs)
- A potential EU AI Act non-compliance (high-risk AI system in HR context)
- A contractual breach of any customer DPA that prohibits sub-processor additions

---

## Regulatory Framework

### GDPR (EU)

**Article 28**: FlowFlex is a data processor for its customers. Any LLM provider is a sub-processor. Requires:
- Written DPA with each LLM provider
- Customer must approve FlowFlex's sub-processor list (or auto-approve via DPA)
- Customer can object to sub-processor addition — FlowFlex must accommodate

**Chapter V (Transfer Mechanism)**: Data leaving EU/EEA requires one of:
- Adequacy decision (US → EU-US Data Privacy Framework as of 2023 — active)
- Standard Contractual Clauses (SCCs) with supplementary measures
- Binding Corporate Rules

**Article 22**: Automated decision-making with "legal or similarly significant effects" requires human review option. Applies to: AI performance scoring, AI-based loan/credit decisions, AI HR screening.

### EU AI Act (effective 2026)

**High-risk AI systems** (Annex III) include:
- AI in employment (recruitment screening, performance assessment, task allocation)
- AI in access to education
- AI in creditworthiness assessment

High-risk requires: conformity assessment, technical documentation, human oversight mechanism, accuracy/robustness/cybersecurity requirements, registration in EU database.

**FlowFlex AI features that may be high-risk:**
- AI Recruiting / CV screening → high-risk (employment category)
- AI Performance Insights → high-risk (employment category)
- AI Credit/Revenue scoring → high-risk (creditworthiness)
- AI Customer Service Bot → likely not high-risk (general-purpose AI assistant)
- AI Document Processing → likely not high-risk (clerical tool)

### UK GDPR

Post-Brexit equivalent. ICO guidance follows GDPR closely. US transfers via UK-US data bridge (similar to EU-US DPF).

---

## Architecture Decisions

### Decision 1: LLM Routing by Data Sensitivity

Not all AI requests carry personal data. Route based on sensitivity:

```
AI Request
    ↓
SensitivityClassifier (analyses request context)
    ↓
┌─────────────────────────────────┐
│ No PII detected (low sensitivity)│ → External LLM (OpenAI / Anthropic)
│ PII present (medium)             │ → External LLM with PII masking
│ High-risk AI Act category        │ → EU-hosted LLM only (Mistral, Azure EU)
│ Customer opted for on-prem       │ → Customer's own LLM endpoint
└─────────────────────────────────┘
```

**PII Masking before external send:**
```php
class PIIMaskingPreprocessor
{
    public function mask(string $prompt, array $piiEntities): MaskedPrompt
    {
        // Replace: "John Smith (john@acme.com)" → "PERSON_1 (EMAIL_1)"
        // Keep mapping for response de-masking
        // Return masked prompt + substitution map
    }
}
```

Limitation: masking breaks contextual coherence for some tasks (meeting transcripts, email drafting). Must be optional and configurable per feature.

### Decision 2: EU Data Residency Option

For customers who require EU data residency:
- Azure OpenAI EU regions (Sweden Central, France Central) — same API, EU data boundary
- Mistral AI (French company, EU-hosted) — strong alternative for text tasks
- Cohere (EU data residency option) — embeddings, rerank
- Self-hosted option: Ollama-compatible endpoint — customer provides, FlowFlex calls

Config per company:
```php
// companies.ai_config JSON column
{
    "llm_provider": "azure_eu",        // openai | azure_eu | mistral | custom
    "custom_endpoint": null,           // customer's own LLM endpoint
    "data_residency": "eu",            // eu | us | any
    "pii_masking": true,               // default true for EU companies
    "high_risk_ai_enabled": false      // EU AI Act high-risk features opt-in
}
```

### Decision 3: Data Retention Post-Inference

LLMs do not retain conversation history between requests (stateless). But FlowFlex may:
- Log prompts and responses for debugging/audit
- Cache responses for identical requests
- Store AI-generated content (summaries, drafts) in the database

Policy:
| Data Type | Retention | Justification |
|---|---|---|
| Raw prompts sent to LLM | 30 days | Debug, abuse detection |
| LLM responses (intermediate) | 30 days | Debug |
| AI-generated content saved by user | User-controlled (same as other content) | User owns their data |
| PII substitution maps | Deleted immediately after response de-masking | No reason to retain |
| Prompt logs for model training | Never send to LLM providers for training | DPA requirement |

### Decision 4: Sub-Processor List Management

```
FlowFlex customers sign DPA → includes sub-processor list
Sub-processors:
  - OpenAI (US) — AI Copilot, text generation
  - Anthropic (US) — Claude fallback
  - Azure OpenAI (EU) — EU data residency option
  - AssemblyAI / Deepgram (US) — Meeting transcription
  - Google Cloud Vision (EU region) — Document OCR

Customer notification: 30-day notice before adding new sub-processor
Customer opt-out mechanism: disable AI features or switch to EU-only routing
```

---

## EU AI Act High-Risk Compliance Checklist

For any feature classified as high-risk under Annex III:

- [ ] Technical documentation (system architecture, training data, validation results)
- [ ] Human oversight mechanism (user can review, override, or opt-out of AI decision)
- [ ] Accuracy metrics documented and disclosed to customers
- [ ] Bias testing against protected characteristics (gender, age, nationality) for HR AI
- [ ] Logging of each AI decision (for audit, minimum 10 years for employment AI)
- [ ] Register in EU AI Act database (post-August 2026 enforcement)
- [ ] Conformity assessment (self-assessment for most Annex III categories)

Implementation: `AiHighRiskAuditLog` table for all high-risk decisions:
```
ai_high_risk_audit_log {
    ulid id PK
    ulid company_id FK
    string feature          // "ai_recruiting" | "ai_performance" | "ai_credit"
    string subject_type     // "employee" | "contact" | "company"
    ulid subject_id FK
    json input_data         // what data was fed to AI
    json ai_output          // AI recommendation/score
    boolean human_reviewed
    ulid reviewed_by FK
    string final_decision   // human may override AI
    timestamp created_at
}
```

---

## Feature Flag: AI Opt-In

All AI features default to **off** for EU companies. Admin must explicitly enable:
```php
// Module key: ai.copilot, ai.meeting-intelligence, ai.recruiting
// Activation flow: Admin → AI & Automation → Enable Feature →
//   Show: data processing disclosure, sub-processor list, EU AI Act category →
//   Require: DPA acknowledgement checkbox → Activate
```

---

## Related

- [[MOC_AI]] — all AI features subject to this framework
- [[left-brain/domains/10_legal/dsar-self-service-portal.md]] — DSAR covers AI-generated data
- [[concept-multi-tenancy]] — per-company AI config
- [[left-brain/architecture/analytics-data-architecture.md]] — analytics data used for AI training
