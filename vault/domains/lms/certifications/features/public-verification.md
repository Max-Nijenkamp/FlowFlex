---
domain: lms
module: certifications
feature: public-verification
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Public Verification

Anyone can confirm a certificate's validity by its number, without logging in.

## Behaviour

- `GET /verify/{number}` looks up a certificate by `certificate_number`.
- Returns `valid` / `expired` / `not-found` plus the `course_title` — no learner PII.
- Rate-limited; numbers are globally-unique + non-sequential so they can't be enumerated across companies.

## UI

- **Kind**: public-vue  <!-- unauthenticated Vue + Inertia page, ui-strategy row #16 -->
- **Page**: "Verify Certificate" (`/verify/{number}`, `Verify.vue`).
- **Layout**: single card — status badge (green valid / amber expired / grey not-found), course title, issue/expiry dates (no name). Optional lookup form when no number in the URL.
- **Key interactions**: land on `/verify/{number}` → status resolves; or type a number → submit (throttled).
- **States**: empty (no number → lookup form) · loading (checking) · error (rate-limited → "Try again shortly") · result (valid / expired / not-found).
- **Gating**: none (public). Rate limiter is the control.

## Data

- Owns / writes: nothing (read-only).
- Reads: `lms_certificates` by number (bypasses `CompanyScope`, exposes only non-tenant data).
- Cross-domain writes: NONE.

## Relations

- Consumes: nothing.
- Feeds: nothing.
- Shared entity: certificate (own module).

> [!warning] UNVERIFIED
> Non-enumerability rests on `FF-{ulid26}` numbers being non-sequential *(assumed)* — see [[../unknowns]]. Rate-limit + minimal payload are the tested controls.

## Related

- [[../_module|Certifications module]] · [[../security]] · [[../api]] · [[../../../../frontend/_index|Frontend]]
