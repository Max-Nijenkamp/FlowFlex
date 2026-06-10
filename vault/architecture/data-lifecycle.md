---
type: architecture
category: data
pattern-key: gdpr
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# Data Lifecycle — GDPR, Retention, Erasure

What happens to data over its whole life: retention periods, the erasure cascade rules per table family, and DSAR export scope. The `core.privacy` module implements the workflows; this doc is the policy any module must follow.

---

## The Three Lifecycle Operations

| Operation | Trigger | What happens |
|---|---|---|
| **Soft delete** | normal app delete | `deleted_at` set; data intact; recoverable |
| **Anonymise** | GDPR erasure request (DSAR) | PII overwritten in place; record skeleton kept for referential + financial integrity |
| **Hard delete (purge)** | scheduled command after retention window | rows physically removed |

**Erasure ≠ delete.** GDPR erasure anonymises the person, it does not destroy business records (invoices stay, legally required).

---

## Per-Table-Family Cascade Rules

When a **person** (employee / user / contact) is erased:

| Table family | Rule | Detail |
|---|---|---|
| `hr_employees` | **Anonymise** | per `EraseEmployeePersonalData`: encrypted fields → null, names → `[Erased]`, work email → `erased_{id}@flowflex-erased.invalid` ([[architecture/patterns/encryption]]) |
| `hr_emergency_contacts` | **Hard delete** | pure PII of third parties, no business value |
| `hr_leave_requests`, `hr_leave_balances` | **Keep** | reference anonymised employee; needed for balance/payroll history |
| `hr_payslips`, payroll records | **Keep 7 years** | legal retention (fiscal law) overrides erasure; salary already encrypted; then purge |
| `users` | **Anonymise** | name → `[Erased]`, email anonymised, password randomised, tokens revoked, avatar deleted |
| `crm_contacts` | **Anonymise** | name/email/phone overwritten; activity history kept linked to skeleton |
| `crm_deals`, invoices, orders | **Keep** | business records; person references point at anonymised skeleton |
| `fin_invoices`, GL entries | **Keep 7–10 years** | statutory bookkeeping retention (NL: 7y, with property 10y); NEVER erased on DSAR |
| Activity log rows (actor) | **Keep, re-attribute** | `causer` points at anonymised user; log content itself must never contain raw PII (rule below) |
| Media/files uploaded BY the person | **Keep** | company documents, ownership re-attributed |
| Media/files ABOUT the person (ID scans, contracts) | **Hard delete** | via Media Library collection delete |
| `consent_logs`, `dsar_requests` | **Keep** | proof of compliance — keep the request that triggered erasure |
| Notifications, comments mentioning the person | **Keep** | body text is company speech; author anonymised |

**Rule for new modules**: every v2 spec whose tables contain person-PII must state which rule each table follows. Default when unstated: Keep (anonymise the referenced person, not the record) — flag with `*(assumed)*` if uncertain.

**Activity log PII rule**: never log raw PII values in activitylog `properties` (log field *names* changed, not old/new values, for encrypted/PII fields).

---

## Company Lifecycle (subscription states)

| State | Data behavior |
|---|---|
| `trial` / `active` | normal |
| `suspended` (payment failed) | access blocked by middleware; data untouched |
| `cancelled` | workspace archived; **90-day retention window** starts; owner can export until purge |
| purge (day 90) | scheduled `PurgeCancelledCompaniesCommand`: hard delete all company rows across every `company_id`-scoped table, R2 files under `companies/{id}/`, search index docs; keep: invoices FlowFlex issued to that company (our own bookkeeping), minimal company row tombstone *(assumed)* |

Purge command must be idempotent and chunked (table by table, FK-safe order), and log a completion record.

---

## Retention Periods

| Data | Retention | Source |
|---|---|---|
| Financial records (invoices, GL, payslips) | 7 years (10 with property) | statutory (NL/EU) |
| Cancelled company workspace | 90 days post-cancellation | [[product/pricing-model]] |
| DSAR + consent logs | duration of company + 3 years *(assumed)* | compliance proof |
| Audit log (activitylog) | 2 years rolling *(assumed)*, then pruned | storage hygiene |
| Failed jobs, telescope, pulse | 30 days *(assumed)* | ops hygiene |
| Soft-deleted rows (normal deletes) | purged 12 months after `deleted_at` *(assumed)* | scheduled prune |

Per-company retention overrides: configurable in Company Settings per data type (core.privacy feature) — overrides may only LENGTHEN beyond defaults, never shorten below statutory.

---

## DSAR Flows (core.privacy implements)

**Access request**: export all rows referencing the subject across domains → ZIP (CSV per model) → 30-day deadline tracker. Scope: any table whose spec lists person-PII; modules register their PII tables in a `PersonalDataRegistry` *(assumed — implemented in core.privacy)*.

**Erasure request**: validate identity → check legal holds (open invoices, employment ongoing → partial erasure only) → run per-family rules above inside a queued, company-scoped job → record completion on the DSAR request → notify requester. Fires `DSARRequestSubmitted` → Legal + Notifications per [[architecture/event-bus]].

**Company export** (data portability): full dataset ZIP from company settings at any time — baseline feature, not enterprise add-on.

---

## Related

- [[domains/core/data-privacy]] — the implementing module
- [[architecture/patterns/encryption]] — erasure of encrypted fields
- [[product/pricing-model]] — cancellation + 90-day window
- [[domains/legal/_index]] — DSAR processing surface (Phase 3)
