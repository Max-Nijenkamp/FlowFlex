---
tags: [flowflex, ai, strategy, platform, phase/6]
domain: Platform
status: planned
last_updated: 2026-05-08
---

# AI Strategy

AI is not a feature at FlowFlex — it is infrastructure. Every domain gets AI capabilities through shared services, not separate add-ons. This is what separates FlowFlex from competitors who bolted AI on top of legacy architecture.

---

## The AI Positioning

**Other platforms:** Added AI as a premium tier, with limited data access and per-feature pricing.

**FlowFlex:** AI has full access to all your business data from day one. The same record that HR edits, Finance invoices against, and CRM tracks — AI can read, summarise, and act on. This cross-domain intelligence is impossible for single-domain competitors to replicate.

---

## AI Capabilities by Domain

| Domain | AI Capabilities |
|---|---|
| **HR & People** | CV parsing & scoring, JD generation, bias detection, attrition prediction, burnout signals |
| **Projects & Work** | Task prioritisation, deadline risk, workload balancing, OKR auto-update from data |
| **Finance** | Transaction auto-categorisation, anomaly detection, cash flow forecasting, invoice chasing |
| **CRM & Sales** | Deal health scoring, next best action, email drafts, win/loss analysis |
| **Marketing** | Content generation, campaign performance insights, email subject optimisation |
| **Operations** | Predictive maintenance, demand forecasting, route optimisation |
| **Analytics** | Natural language queries, auto-generated narratives, anomaly detection |
| **IT & Security** | Threat detection, access pattern anomalies, compliance gap analysis |
| **Legal** | Contract risk scoring, clause extraction, renewal alerts |
| **E-commerce** | Product recommendations, demand forecasting, returns prediction |
| **Communications** | Meeting summaries, message drafts, smart scheduling |
| **L&D** | Personalised learning paths, quiz generation, skills gap analysis |
| **AI & Automation** | Workflow builder (the AI backbone), copilot, autonomous agents |
| **Community** | Moderation, content categorisation, engagement insights |

---

## AI Infrastructure (Shared Layer)

All AI features are powered by the same shared infrastructure in [[AI Infrastructure]]:

### LLM Routing

```
User request
  → Route by: task type, required context size, cost tier, latency requirement
  → Simple tasks (classify, draft short text): GPT-4o-mini / Gemini Flash
  → Complex tasks (multi-step reasoning, long context): GPT-4o / Claude Sonnet
  → Batch/background tasks: cheapest capable model
  → On-premise/air-gap: Ollama (Llama 3.1, Mistral)
```

### Data Access

- AI operates entirely within the tenant's data scope
- Same RBAC applies: if the user can't see a record, neither can their AI assistant
- No cross-tenant data leakage — company data never used to train models or answer other tenants' queries

### Privacy-First by Default

- PII masking before sending to external LLMs (configurable per field type)
- Zero-retention requests where provider supports it (OpenAI, Anthropic)
- AI query audit log stored internally — full transparency on what was asked/answered
- GDPR: AI history treated as personal data, deleted on data erasure request

### Cost Management

- Token usage tracked per feature, per user, per company
- Monthly AI budget per company (configurable per plan tier)
- Hard limit at budget threshold: feature degrades gracefully (slower model or no AI)
- Cost-per-action benchmarks published for common use cases

---

## AI Feature Tiers

| Feature | Starter | Pro | Enterprise |
|---|---|---|---|
| AI Copilot (read-only queries) | 100 queries/day | 500/day | 2,000/day |
| AI Copilot (write actions) | 20 actions/day | 100/day | 500/day |
| AI Agents | ❌ | 3 active agents | Unlimited |
| Workflow AI steps | 100/month | 1,000/month | Unlimited |
| Document AI analysis | 10/day | 50/day | Unlimited |
| Custom AI prompts | ❌ | ✅ | ✅ |
| Local LLM / Ollama | ❌ | ❌ | ✅ |
| Dedicated AI budget | ❌ | ❌ | ✅ |

---

## AI Roadmap

### Phase 6 (current planning)
- AI Assistant & Copilot — cross-domain chat
- Workflow Automation Builder — AI action steps
- AI Agents — 8 pre-built + custom builder
- Integration Hub — 200+ connectors
- AI Infrastructure — LLM management, cost controls
- Per-domain AI features (HR CV parsing, Finance categorisation, CRM deal scoring)

### Phase 7
- Voice interface — speak to your AI assistant
- AI-generated reports — natural language → visual report
- Predictive HR — attrition and hiring need forecasting
- AI customer success — health score monitoring and automated intervention
- Community AI moderation — fully automated with human review queue

### Phase 8
- Custom model fine-tuning on customer's data (Enterprise)
- Automated insights delivery (weekly AI business review email)
- Multi-agent orchestration — complex multi-step autonomous workflows
- AI-powered onboarding — AI walks new companies through their setup

---

## What FlowFlex AI Is NOT

- Not a general-purpose chatbot (it knows your business, not the internet)
- Not trained on customer data (privacy-first, always)
- Not a replacement for human judgment (AI suggests, humans decide on critical actions)
- Not a separate product requiring a separate subscription

---

## Related

- [[AI Overview]]
- [[AI Assistant & Copilot]]
- [[AI Agents]]
- [[Workflow Automation Builder]]
- [[AI Infrastructure]]
- [[FlowFlex Overview]]
