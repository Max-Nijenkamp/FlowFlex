---
type: adr
date: 2026-06-10
status: decided
color: "#F97316"
---

# No Public Self-Registration — Invite-Only, Staff-Created Companies

---

## Context

The vault contained two contradictory descriptions of how companies and users enter FlowFlex:

- `frontend/_index.md` listed a public `/register` page ("Company + owner registration") and public Vue onboarding wizard pages (`/onboarding`, `/onboarding/modules`). `architecture/security.md` showed an open `Route::post('/register')` in its SPA auth example.
- `domains/foundation/_index.md` states as a Key Constraint: "No public company registration — companies created by FlowFlex staff in `/admin`". `domains/core/invitation-system.md` states invites are "the only way new users join a company workspace (no open self-registration)". `architecture/module-system.md` shows `CompanyCreationService` invoked by staff in `/admin`. `domains/core/setup-wizard.md` implements first-login onboarding as a custom Filament page in `/app`, not public Vue pages.

The public-registration references were leftovers from an earlier vault iteration and conflicted with the specced module set.

---

## Options Considered

1. **Public self-registration** — open `/register` creates company + owner, public Vue onboarding wizard. Self-serve funnel, but contradicts `core.invitations`, `core.setup`, and the Foundation constraint; requires anti-abuse work (spam companies, trial fraud) not specced anywhere.
2. **Invite-only, staff-created companies** — FlowFlex staff create companies in `/admin`; owner receives an invite; users join only via `core.invitations`; first-login setup via the `core.setup` Filament wizard. Matches all existing module specs and the sales-assisted land-and-expand motion in `product/positioning.md`.

---

## Decision

Option 2 — invite-only.

- Companies are created by FlowFlex staff in `/admin` (`CompanyCreationService`).
- The only public registration surface is invite acceptance: `/register/invite/{token}` (`Pages/Auth/InviteRegister.vue`, name + password, email pre-filled).
- First-login company setup runs in the `/app` Filament Setup Wizard (`core.setup`), not public Vue pages.
- No open `/register` route exists.

Corrected files: `frontend/_index.md`, `architecture/security.md`, `domains/core/setup-wizard.md` (Related link), `build/BUILD-ORDER.md` (MVP gate wording).

---

## Consequences

- Public Vue auth surface is limited to: login, logout, password reset, email verification, invite acceptance.
- Signup funnel is sales-assisted — no self-serve trial signup in v1. If self-serve signup is wanted later, it becomes a new module spec plus a revisit of this ADR (anti-abuse, trial provisioning, payment-first flow).
- Playwright E2E covers login and invite acceptance instead of an onboarding wizard flow.
- No anti-abuse/captcha work needed for company creation in MVP.

---

## Related

- [[domains/core/invitation-system]]
- [[domains/core/setup-wizard]]
- [[domains/foundation/_index]]
- [[architecture/security]]
- [[architecture/module-system]]
