---
type: module
domain: AI & Automation
panel: ai
module: EU AI Act Compliance
phase: 4
status: complete
cssclasses: domain-ai
migration_range: 460010
last_updated: 2026-05-12
right_brain_log: "[[builder-log-ai-phase6]]"
---

# EU AI Act Compliance

Tools for classifying AI features by risk level, generating required transparency documentation, logging human oversight decisions, and maintaining an AI systems register as required by the EU AI Act (Regulation 2024/1689, fully in force August 2026).

---

## EU AI Act Timeline

| Date | Requirement |
|---|---|
| Feb 2025 | Prohibited AI practices ban (Art. 5) |
| Aug 2025 | GPAI model obligations (Art. 51-55) |
| Aug 2026 | High-risk AI obligations + transparency rules (Art. 6-49) |

---

## Risk Classification

| Risk Level | Examples | Obligations |
|---|---|---|
| Prohibited | Social scoring, real-time biometric surveillance | Cannot use |
| High Risk | HR screening AI, credit scoring, biometrics | Full compliance obligations |
| Limited Risk | Chatbots, deepfakes | Transparency disclosure |
| Minimal Risk | Spam filters, AI recommendations | No requirements |

FlowFlex AI features to classify:
- AI Document Processing → Limited Risk (OCR + extraction)
- AI Customer Service Bot → Limited Risk (chatbot → must disclose AI)
- AI Meeting Intelligence → Minimal Risk
- AI Price Optimisation → Limited Risk  
- Talent Intelligence / HR scoring → **High Risk if used for hiring decisions**
- Credit scoring / risk assessment → **High Risk**

---

## Key Tables

```sql
CREATE TABLE ai_systems_register (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    name            VARCHAR(255) NOT NULL,
    description     TEXT,
    risk_level      ENUM('prohibited','high','limited','minimal'),
    risk_rationale  TEXT,
    use_case        TEXT,
    affected_groups TEXT,
    data_inputs     TEXT,
    decision_output TEXT,
    human_oversight_required BOOLEAN DEFAULT FALSE,
    -- Provider info (for third-party AI)
    ai_provider     VARCHAR(100) NULL,
    model_name      VARCHAR(100) NULL,
    -- Compliance status
    status          ENUM('draft','compliant','non_compliant','under_review','not_applicable'),
    last_assessed_at DATE NULL,
    next_review_at  DATE NULL,
    -- Documentation links
    technical_docs_path VARCHAR(500) NULL,
    instructions_path   VARCHAR(500) NULL,
    created_by      ULID NOT NULL REFERENCES users(id),
    created_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE ai_human_oversight_log (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    system_id       ULID NOT NULL REFERENCES ai_systems_register(id),
    decision_id     VARCHAR(255),        -- ID of the AI-influenced decision
    ai_recommendation TEXT,
    human_decision  TEXT,
    human_overrode  BOOLEAN,
    override_reason TEXT NULL,
    decided_by      ULID NOT NULL REFERENCES users(id),
    decided_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE ai_transparency_notices (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    system_id       ULID NOT NULL REFERENCES ai_systems_register(id),
    notice_text     TEXT NOT NULL,       -- shown to users before AI interaction
    active          BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMP DEFAULT NOW()
);
```

---

## Compliance Checklist (High Risk)

For each High Risk AI system:
- [ ] Technical documentation (Art. 11)
- [ ] Logging and audit trail (Art. 12)
- [ ] Transparency notice for affected persons (Art. 13)
- [ ] Human oversight mechanism (Art. 14)
- [ ] Accuracy, robustness, cybersecurity measures (Art. 15)
- [ ] Conformity assessment completed
- [ ] Registration in EU database (Art. 71)

---

## ChatBot Disclosure

For Limited Risk (chatbots): FlowFlex Customer Service Bot must always display:
"You are interacting with an AI assistant."

`ai_transparency_notices` stores the notice text, displayed at session start.  
Platform enforces: cannot deploy customer-facing AI chat without an active notice.

---

## Related

- [[MOC_AI]]
- [[ai-customer-service-bot]]
- [[MOC_Legal]]
- [[ai-gdpr-data-residency]]
