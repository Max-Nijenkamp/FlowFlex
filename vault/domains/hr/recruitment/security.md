---
domain: hr
module: recruitment
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Recruitment — Security

Intended controls. Nothing enforced yet — see [[_module]]. Cross-refs: [[../../../security/encryption]] · [[../../../security/authn-authz]] · [[../../../security/tenancy-isolation]] · [[../../../security/data-privacy-gdpr]].

---

## Permissions

`hr.recruitment.view-any` · `hr.recruitment.view` · `hr.recruitment.create` · `hr.recruitment.update` · `hr.recruitment.delete` · `hr.recruitment.hire` · `hr.recruitment.manage-offers`

---

## Authorization

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('hr.recruitment.view-any')
              && BillingService::hasModule('hr.recruitment')
```

Custom pages (`ApplicantPipelinePage`) state this explicitly. State transitions are permission-guarded (`update` for pipeline moves, `hire` for the hire transition). See [[../../../security/authn-authz]].

---

## Tenancy

All 4 tables carry `company_id` (indexed) + `BelongsToCompany` + `CompanyScope`. The public apply path resolves and validates the company from the requisition slug — no cross-tenant leakage. See [[../../../security/tenancy-isolation]].

---

## Encrypted fields

- `hr_offers.salary_raw` — encrypted cast, `text` column. Stores a minor-unit integer as an encrypted string; use brick/money for arithmetic. Mirrors hr.payroll / hr.compensation. See [[../../../security/encryption]].

---

## Public / portal guard (HIGH)

Public submission goes through a **guest / unauthenticated** controller (no Sanctum session). Company is resolved + validated from the requisition slug, with explicit input validation and abuse controls. The guard boundary must be documented in the controller.

## Rate limiter (medium)

| Action | Limiter | Category |
|---|---|---|
| Public application submission | `public-apply` (keyed per IP + per requisition) | public token endpoint |
| `sendOffer` → `OfferMail` | `panel-action` | comms |
| Rejection → `RejectionMail` | `panel-action` | comms |

Named limiters per [[../../../architecture/security]] and [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]].

## Upload contract (medium)

CV files store under `companies/{company_id}/recruitment/` on a **private disk**, enforcing the pdf/docx + max 10MB type/size rules, matching the file-storage tenant path convention.

---

## Candidate PII / GDPR

Applicant records hold PII (name, email, phone) plus resume files. Retention: applicant data retained 12 months, then purged by `PurgeStaleApplicantsCommand` (rejected/withdrawn > 12 months, date guard) *(assumed)*. See [[../../../security/data-privacy-gdpr]].

---

## Related

- [[_module]] · [[data-model]] · [[api]]
