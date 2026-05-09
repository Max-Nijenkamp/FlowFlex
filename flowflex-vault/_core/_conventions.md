---
type: meta
status: stable
last_updated: 2026-05-08
---

# Vault Conventions

## File Naming

```
left-brain/domains/01_core-platform/MOC_CorePlatform.md
left-brain/domains/01_core-platform/authentication.md
left-brain/entities/company.md
left-brain/concepts/multi-tenancy.md
right-brain/builder-logs/authentication.md
right-brain/gaps/gap_missing-oauth-scopes.md
```

Rules:
- MOC files: `MOC_<Name>.md` (PascalCase after prefix)
- Module files: `kebab-case.md`
- Entity files: `kebab-case.md` (singular: `company.md` not `companies.md`)
- Gap files: `gap_<description>.md`
- Builder logs: match the module filename

---

## Frontmatter Standards

### Module note

```yaml
---
type: module
domain: Core Platform
panel: admin           # filament panel slug
phase: 1
status: complete | in-progress | planned
migration_range: 000000–009999
last_updated: YYYY-MM-DD
---
```

### Entity note

```yaml
---
type: entity
domain: Platform
table: companies
primary_key: ulid
soft_deletes: true
last_updated: YYYY-MM-DD
---
```

### MOC

```yaml
---
type: moc
section: left-brain/architecture
last_updated: YYYY-MM-DD
---
```

### Concept

```yaml
---
type: concept
category: architecture | security | data | ux
last_updated: YYYY-MM-DD
---
```

---

## Linking

- Always use `[[filename]]` (Obsidian resolves by filename, not path)
- If two files share a name, use `[[path/to/filename|Display Name]]`
- Every note MUST link to its parent MOC
- Module notes link: parent MOC + relevant entities + related modules
- Right Brain notes link: source Left Brain module note

---

## Mermaid Standards

Every MOC includes at least one Mermaid diagram.

```mermaid
graph TD   ← use for hierarchies and domain maps
graph LR   ← use for flows and pipelines
erDiagram  ← use for data models
sequenceDiagram ← use for request flows and event sequences
```

Node naming in architecture diagrams:
- `Client["Browser / Mobile"]`
- `LB["Load Balancer (Nginx)"]`
- subgraphs for logical groupings

---

## Status Values

| Value | Meaning |
|---|---|
| `complete` | Built, tested, in production |
| `in-progress` | Currently being built |
| `planned` | Specced, not yet built |
| `research` | Being explored, not yet specced |

---

## Migration Range Registry

| Domain | Range |
|---|---|
| Core Platform | 000000–099999 |
| HR & People | 100000–149999 |
| Projects & Work | 150000–199999 |
| Finance & Accounting | 200000–249999 |
| CRM & Sales | 250000–299999 |
| Operations | 300000–399999 |
| Marketing & Content | 400000–449999 |
| Analytics & BI | 450000–499999 |
| IT & Security | 500000–549999 |
| Legal & Compliance | 550000–599999 |
| E-commerce | 600000–649999 |
| Communications | 650000–699999 |
| Learning & Development | 700000–749999 |
| AI & Automation | 750000–799999 |
| Community & Social | 800000–849999 |
| Workplace & Facility | 850000–869999 |
| Professional Services (PSA) | 870000–889999 |
| Product-Led Growth | 890000–909999 |
| Business Travel | 910000–929999 |
| ESG & Sustainability | 930000–949999 |
| Real Estate & Property | 950000–969999 |
| Customer Success | 970000–974999 |
| Subscription Billing & RevOps | 975000–979999 |
| Procurement & Spend Management | 980000–984999 |
| Financial Planning & Analysis (FP&A) | 985000–989999 |
| Events Management | 990000–994999 |
| Document Management | 995000–999999 |
| Whistleblowing & Ethics Hotline | 1000000–1049999 |
| Field Service Management | 1050000–1099999 |
| Pricing Management | 1100000–1149999 |
| Enterprise Risk Management | 1150000–1199999 |
