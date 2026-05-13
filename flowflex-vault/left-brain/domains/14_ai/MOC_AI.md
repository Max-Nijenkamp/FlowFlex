---
type: moc
domain: AI & Automation
panel: ai
cssclasses: domain-ai
phase: 6
color: "#6366F1"
status: complete
last_updated: 2026-05-12
right_brain_log: "[[builder-log-ai-phase6]]"
---

# AI & Automation — Map of Content

The intelligence layer. Workflow automation builder, AI assistant/copilot, autonomous AI agents, 200+ integrations, smart notifications, and LLM infrastructure.

**Panel:** `ai`  
**Phase:** 6  
**Migration Range:** `750000–799999`  
**Colour:** Indigo-500 `#6366F1` / Light: `#EEF2FF`  
**Icon:** `heroicon-o-sparkles`

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| Workflow Automation Builder | 6 | complete | No-code trigger/action automation across all domains |
| AI Assistant & Copilot | 6 | complete | Cross-domain AI chat with full data access |
| AI Agents | 6 | complete | Autonomous background agents for recurring operations |
| Integration Hub | 6 | complete | 200+ third-party app connectors (REST, webhooks, OAuth) |
| Smart Notifications & Triggers | 6 | complete | Intelligent alert routing, escalation, digest |
| AI Infrastructure | 6 | complete | LLM management, cost controls, privacy, prompt templates |
| [[ai-document-processing\|AI Document Processing & OCR]] | 6 | complete | Auto-extract data from invoices, receipts, contracts, POs |
| [[ai-meeting-intelligence\|AI Meeting Intelligence]] | 6 | complete | Auto-transcription, structured summaries, action item extraction |
| [[ai-customer-service-bot\|AI Customer Service Bot]] | 6 | complete | Conversational AI for L1/L2 support deflection, seamless escalation |
| [[eu-ai-act-compliance\|EU AI Act Compliance]] | 4 | complete | AI systems register, risk classification, transparency notices, human oversight log |

---

## Key Capabilities

### Workflow Automation Builder

Replaces Zapier/Make:
- Trigger types: event-based, schedule, webhook, data condition
- Action types: create/update records, send emails/SMS, call webhooks, run AI step
- Branching (if/else), loops, wait steps
- Pre-built playbooks per domain

### AI Assistant & Copilot

- Chat sidebar available in every panel
- Context-aware: knows current record and module
- Can take actions: create tasks, draft emails, generate reports
- Access control: only sees data user has permission for

### AI Agents

Autonomous agents that run on schedule:
- **Invoice agent**: chase overdue invoices, draft emails, escalate
- **Recruitment agent**: screen CVs, schedule interviews, send updates
- **Support agent**: classify tickets, suggest replies, auto-resolve L1
- **Inventory agent**: monitor stock, create POs, alert on anomalies
- Custom agents via no-code agent builder

### Integration Hub

- OAuth connectors: Google Workspace, Microsoft 365, Slack, Stripe, etc.
- API key connectors: 150+ SaaS tools
- Webhook in/out
- Zapier/Make import (migrate existing automations)

---

## Permissions Prefix

`ai.automations.*` · `ai.copilot.*` · `ai.agents.*`  
`ai.integrations.*` · `ai.notifications.*` · `ai.infrastructure.*`

---

## Competitors Displaced

Zapier · Make (Integromat) · Microsoft Copilot · n8n · Salesforce Einstein

---

## Related

- [[MOC_Domains]]
- [[MOC_Analytics]] — AI Insights Engine lives in Analytics domain
- [[concept-event-driven]] — automation engine listens to all domain events
