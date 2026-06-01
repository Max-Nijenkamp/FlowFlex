---
type: domain-index
domain: Legal & Compliance
panel: legal
color: "#4ADE80"
---

# Legal & Compliance

Contract management, compliance registers, matter management, legal spend, policy library, and DSAR processing. **Panel:** `/legal` (Amber) — Phase 3.

---

## Navigation Groups

- **Contracts** — Legal Contracts
- **Matters** — Matter Management
- **Spend** — Legal Expenses, Budgets
- **Compliance** — Frameworks, Controls, Policies, DSAR Requests

---

## Modules

| Module | Key | Status | Priority |
|---|---|---|---|
| [[domains/legal/legal-contracts\|Legal Contracts]] | `legal.contracts` | planned | **P3 core** |
| [[domains/legal/compliance-registers\|Compliance Registers]] | `legal.compliance` | planned | P3 |
| [[domains/legal/matter-management\|Matter Management]] | `legal.matters` | planned | P3 |
| [[domains/legal/legal-spend\|Legal Spend]] | `legal.spend` | planned | P3 |
| [[domains/legal/policy-library\|Policy Library]] | `legal.policies` | planned | P3 |
| [[domains/legal/dsar-processing\|DSAR Processing]] | `legal.dsar` | planned | P3 |

---

## Key Patterns

- `spatie/laravel-model-states` — contract status, matter status, DSAR status
- `awcodes/filament-tiptap-editor` — policies
- Cross-domain: `DSARErasureRequested` → all domains anonymise
- Deepens [[domains/core/data-privacy]]; uses [[architecture/patterns/encryption]] for erasure
- Legal spend posts to [[domains/finance/accounts-payable]]
