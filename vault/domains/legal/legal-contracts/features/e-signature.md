---
domain: legal
module: legal-contracts
feature: e-signature
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# E-signature

Capture a signed contract. v1 = manual signed-PDF upload; native/embedded e-sign is a roadmap opportunity.

## Behaviour

- v1: user uploads the countersigned PDF; on upload with `legal.contracts.sign-off`, the contract moves `in_review → signed` and `signed_at` is set.
- PDF-only, size-capped, `companies/{id}/`-scoped Media Library collection ([[../security]]).
- Roadmap: embedded e-sign / signer portal (see [[../_opportunities]]) — a counterparty signs via a scoped public link.

## UI

- **Kind**: custom-page / public-vue
- **Page**: internal upload step on `LegalContractResource` (custom action modal); roadmap external signer surface = Vue/Inertia public page (`/sign/{token}`) — unauthenticated, token-scoped.
- **Layout**: internal — upload dropzone + confirm-signed toggle. Public (roadmap) — document preview + signature capture + submit.
- **Key interactions**: internal — drop PDF → validate PDF-only → confirm → transition to `signed`. Public — review → sign → POST returns signed status.
- **States**: empty (no document yet → upload CTA) · loading (upload progress) · error ("PDF only" / expired token) · selected (signed → badge + `signed_at`).
- **Gating**: internal `legal.contracts.sign-off`; public surface uses a scoped guest/portal guard (token), not a company user session.

## Data

- Owns / writes: `legal_contracts` (`status`, `signed_at`) + the signed-PDF media.
- Reads: none cross-domain.
- Cross-domain writes: none — files handled via `core.files` collection this module owns ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: signature completion is what unblocks the [[./contract-lifecycle|lifecycle]] to `signed`.
- Shared entity: none.

## Unknowns

- `*(assumed)*` manual-PDF convention mirrors crm.contracts; embedded e-sign unscoped — [[../unknowns]] + [[../_opportunities]].

## Related

- [[../_module|Legal Contracts]] · [[./contract-repository]] · [[../_opportunities]]
