---
domain: hr
module: dei-metrics
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# DEI Metrics — Security (CRITICAL)

This module handles **highly sensitive, self-declared diversity attributes**. Every safeguard below is a hard requirement of the rebuild, not optional hardening. See [[_module]].

## Encrypted fields

- `hr_dei_attributes.value` — encrypted at rest (`text` column). **Never indexed, never SQL-filtered.** Values are only decrypted transiently inside the snapshot job and discarded immediately after aggregation.
- Pattern: [[../../../security/encryption]] · [[../../../architecture/patterns/encryption]]

## Consent model

- Collection is strictly **employee opt-in** via HR self-service. A consent checkbox is required to submit.
- Consent is logged via **core.privacy** (`consented_at` references the consent log).
- Withdrawal (`WithdrawDeiConsentAction`) deletes the employee's attribute rows and logs the withdrawal.

## Aggregate-only display + suppression threshold

- **No permission exposes individual attributes — not even `view-any`.** Individual values are never rendered anywhere.
- Dashboards read pre-computed snapshots only; there is no live decrypt-and-group over individuals at request time.
- **Suppression threshold: never show groups smaller than N=5** *(assumed default, configurable)* — k-anonymity protection. Groups below N are suppressed **before** storage in `hr_dei_snapshots.breakdown`; the dashboard shows "insufficient group size" placeholders.
- Boundary must be tested at N-1 (suppressed) and N (shown).

## Permissions

- `hr.dei.view-dashboard` — HR leadership; gates the aggregate dashboard.
- `hr.dei.submit-own` — all employees; self-service opt-in only.
- There is deliberately **no** `view-any` permission — individual attributes are never rendered anywhere. `DeiDashboardPage` gates on `canAccess() = Auth::user()->can('hr.dei.view-dashboard') && BillingService::hasModule('hr.dei')`; the self-service DEI section gates on `hr.dei.submit-own` (own record only).
- Pattern: [[../../../security/authn-authz]]

## Tenancy

- `hr_dei_attributes` and `hr_dei_snapshots` are company-scoped (`company_id` indexed). Cross-tenant reads must be impossible.
- Pattern: [[../../../security/tenancy-isolation]]

## Jurisdiction awareness

- Only collect/report dimensions legal per company country (per-country allowed-dimension config map *(assumed)*). Disallowed dimensions are blocked at submission.

## GDPR erasure

- On employee erasure, DEI attribute rows are **hard-deleted**.
- Consent withdrawal deletes attributes + logs the event.
- Snapshots retain aggregates only — no individual data survives in them.
- Pattern: [[../../../security/data-privacy-gdpr]]

## Related

- [[data-model]]
- [[architecture]]
- [[features/consent-management]]
- [[features/anonymized-snapshots]]
