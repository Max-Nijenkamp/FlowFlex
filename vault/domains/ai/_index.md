---
type: domain-index
domain: AI & Automation
panel: ai
color: "#4ADE80"
---

# AI & Automation

Workflow builder, AI copilot, document intelligence, and model configuration. **Panel:** `/ai` (Indigo) — Phase 3.

**Displaces**: Zapier, Make, Workato

---

## Navigation Groups

- **Workflows** — Workflow Builder, Run History
- **Copilot** — Chat
- **Document Intelligence** — Extractions
- **Settings** — AI Model Configuration, Usage

---

## Modules

| Module | Key | Status | Priority |
|---|---|---|---|
| [[domains/ai/workflow-builder\|Workflow Builder]] | `ai.workflows` | planned | **P3 core** |
| [[domains/ai/copilot\|AI Copilot]] | `ai.copilot` | planned | P3 |
| [[domains/ai/document-intelligence\|Document Intelligence]] | `ai.document-intelligence` | planned | P3 |
| [[domains/ai/model-config\|AI Model Configuration]] | `ai.config` | planned | P3 |

---

## Key Patterns

- Encrypted LLM API keys (see [[architecture/patterns/encryption]])
- All AI data access enforces CompanyScope + user permissions — never cross-tenant
- Prompt injection guardrails (see [[architecture/security]])
- Workflow triggers via [[architecture/event-bus]]; actions via [[architecture/queue-jobs]]
- Token usage metered → usage-based billing candidate (see [[product/pricing-model]])
- Custom pages: visual workflow editor, copilot chat (Vue components)
