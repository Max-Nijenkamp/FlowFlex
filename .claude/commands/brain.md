Read the complete FlowFlex Brain — implementation reality, not product vision.

Read ALL of these files in order. Do not skip any.

## Step 1 — State & Phase
Read `obsidian/_Brain/Current State.md`
- Note: current phase, test count, active panels, Phase 1.5 marketing site status, pending decisions

## Step 2 — Code Patterns (MANDATORY before writing any code)
Read `obsidian/_Brain/Patterns.md`
- Note: model traits, Filament 5 API, BelongsToCompany behaviour, tenant dropdown scoping, permission naming, activity log rules

## Step 3 — Bug Registry (check BEFORE writing any new code)
Read `obsidian/_Brain/Bug Registry.md`
- Note: any bug pattern relevant to the current task — if task touches HR, read HR bugs; Finance → Finance bugs, etc.

## Step 4 — Codebase Map
Read `obsidian/_Brain/Codebase Map.md`
- Note: where the relevant files live for this task — models, resources, factories, routes

## Step 5 — Domain Files (read the domain(s) relevant to the task)
Read one or more of:
- `obsidian/_Brain/Domain — Core Platform.md` — Company, Tenant, User, ApiKey, File, Module, Admin + Workspace panels, Marketing models
- `obsidian/_Brain/Domain — HR.md` — 26 models, 14 resources, all enums with backing values, payroll, leave, onboarding
- `obsidian/_Brain/Domain — Projects.md` — 12 models, 6 resources, tasks, time tracking, documents
- `obsidian/_Brain/Domain — Finance.md` — 10 models, 7 resources, invoicing, expenses, recurring invoices
- `obsidian/_Brain/Domain — CRM.md` — 19 models, 10 resources, contacts, pipeline, support, CSAT, chatbot

## Step 6 — Relations Map (if task involves cross-domain queries or new FKs)
Read `obsidian/_Brain/Relations Map.md`
- Note: cross-domain FKs, any relation that crosses domain boundary

## Step 7 — Test Suite (if task involves writing or running tests)
Read `obsidian/_Brain/Test Suite.md`
- Note: test count, known pitfalls table (12 traps), factory states, test helper pattern

## Step 8 — Features (if task involves a panel or user-facing feature)
Read `obsidian/_Brain/Features.md`
- Note: what's already built in the relevant panel, API endpoints

---

After reading, output a summary with:
1. **Phase:** what's built, what's next
2. **Relevant domain:** key models and their actual field names (from domain file, not memory)
3. **Patterns to apply:** from Patterns.md — specific to this task
4. **Bug patterns to avoid:** from Bug Registry — any entries that match this task's domain
5. **Relevant spec notes:** which `obsidian/00-14/` files to check for product intent

Do NOT modify any files. Read only.
