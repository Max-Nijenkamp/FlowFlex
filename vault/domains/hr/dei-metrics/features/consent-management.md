---
domain: hr
module: dei-metrics
feature: consent-management
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Consent management

## Purpose

Make consent mandatory for collection and give employees a clean withdrawal path, with everything logged via core.privacy.

## Behavior

- Submission requires a consent checkbox; `consented_at` references the consent log in core.privacy.
- `WithdrawDeiConsentAction::run()` deletes the employee's own attribute rows and logs the withdrawal.
- On employee GDPR erasure, attribute rows are hard-deleted; snapshots retain aggregates only.

## Tables / Permissions

- Writes/deletes `hr_dei_attributes`; `consented_at` links to core.privacy.
- Permission: `hr.dei.submit-own` (all employees, own record only).

## UI

- **Kind**: custom-page (consent + withdrawal controls within the `/app` self-service DEI section)
- **Page**: consent controls on "My Profile" (`/app/my-profile`) DEI section — same self-service page as declaration
- **Layout**: required consent checkbox on the declaration form; a separate "Withdraw consent & delete my DEI data" control with a confirm dialog; short copy on what withdrawal does (hard-deletes rows, snapshots keep aggregates only)
- **Key interactions**: tick consent to enable submission; click Withdraw → confirm → `WithdrawDeiConsentAction` deletes own attribute rows and logs the withdrawal
- **States**: empty = no consent yet, submit disabled · loading = withdrawal in progress · error = "Couldn't withdraw" toast · selected = consented state shows a "consent recorded" indicator; after withdrawal, form resets to opt-in · confirming = withdrawal confirm dialog open
- **Gating**: `hr.dei.submit-own` (own record only); consent mandatory before any collection; withdrawal always available to the owner

## Data

- Owns / writes: `hr_dei_attributes` (writes on consent, hard-deletes on withdrawal/erasure); `consented_at` references the core.privacy consent log
- Reads: `hr_employees` (own record); core.privacy consent status
- Cross-domain writes: consent + withdrawal logged via core.privacy consent API (never its tables directly) ([[../../../../security/data-ownership]])

## Relations

- Consumes: employee GDPR-erasure signal from core.privacy / `hr.profiles` → hard-delete own attribute rows (snapshots retain aggregates only) *(assumed event wiring)*
- Feeds: none outbound (privacy); writes consent/withdrawal entries to core.privacy log
- Shared entity: `hr_employees` (read-only), core.privacy consent log

## Test Checklist

### Unit
- [ ] `consented_at` is required (references a core.privacy consent log) before any attribute row is written

### Feature (Pest)
- [ ] `WithdrawDeiConsentAction` hard-deletes the owner's own `hr_dei_attributes` rows and logs the withdrawal
- [ ] Employee GDPR erasure hard-deletes attribute rows; existing snapshots retain aggregates only (no individual data survives)
- [ ] Withdrawal affects only the acting employee's rows — cannot delete another employee's attributes (tenant + ownership isolation)

### Livewire
- [ ] Withdraw control opens a confirm dialog; confirming runs the action and resets the form to opt-in
- [ ] Consent checkbox gates submission; denied without `hr.dei.submit-own`

## Related

- [[../_module]]
- [[../security]]
- [[../../../../security/data-privacy-gdpr]]
