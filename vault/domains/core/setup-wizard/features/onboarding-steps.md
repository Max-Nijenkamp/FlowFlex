---
domain: core
module: setup-wizard
feature: onboarding-steps
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Onboarding Steps

Parent: [[../_module]] · See [[../architecture]]

The revamped **six-step** first-login flow (see [[../_module]]).

1. **Welcome & owner** — confirm owner; prompt strong password + optional 2FA.
2. **Company profile** — name, logo, primary colour, industry, size → company settings.
3. **Localisation** — timezone, locale, base currency, fiscal-year start, number/date format → company settings.
4. **Choose modules** — marketplace quick-pick (`ActivateModuleData` via BillingService). **Pivotal**: activation
   decides the hub's domain tiles + RBAC's assignable permissions. Deferrable (core stays on).
5. **Team & roles** — invite members (`CreateInvitationData`, queued mail) + assign roles limited to step-4
   modules. Skippable.
6. **Finish** — summary → `CompleteSetupAction` sets `companies.setup_completed_at` → redirect to the
   [[../../workspace-hub/_module|Workspace Hub]].

Progress persists between steps (Livewire session state); resume lands on the first incomplete step.

## UI

- **Kind**: custom-page (a stepper wizard — `SetupWizardPage` Livewire).
- **Page**: "Setup" (`/app/setup` — the owner's forced first-login route until complete).
- **Layout**: left progress rail (6 steps, current highlighted) + right step panel + per-step "why this
  matters" hint; back/next; skip on steps 4–5.
- **Key interactions**: fill step → validate → next (state saved); back to revisit; step 4 opens the
  marketplace quick-pick; finish redirects to the hub.
- **States**: default (current step) · loading (saving/activating) · error (per-step validation) · resume
  (lands on first incomplete step) · complete (never shown again — redirect middleware).
- **Gating**: owner only, while `companies.setup_completed_at` is null.

## Data

- Writes: company settings + invitations + module subscriptions **via each owning module's service/action**
  (never those tables directly); sets `companies.setup_completed_at`. No cross-domain table writes
  ([[../../../../security/data-ownership]]).

## Relations

- Feeds: activated modules → [[../../workspace-hub/_module|hub]] tiles + [[../../rbac/features/module-scoped-permissions|assignable permissions]].
- Consumes: nothing.

## Test Checklist

### Unit
- [ ] First-incomplete-step computation resumes at the right step from the presence of settings/invites/activation
- [ ] Skip on steps 4–5 marks them complete without side effects

### Feature (Pest)
- [ ] Steps 2–3 persist into the company settings classes
- [ ] Step 4 activates the chosen module via `BillingService`; step 5 queues invitations
- [ ] `CompleteSetupAction` sets `companies.setup_completed_at` and redirects to the Workspace Hub
- [ ] Wizard only ever writes the authenticated owner's own company (tenant isolation)

### Livewire
- [ ] Owner with null `setup_completed_at` reaches `SetupWizardPage`; a completed owner is redirected away
- [ ] Non-owner cannot access the page; per-step validation blocks next until valid

## Related

- [[../_module]] · [[../architecture]] · [[../../workspace-hub/_module]] · [[../../rbac/_module]]
