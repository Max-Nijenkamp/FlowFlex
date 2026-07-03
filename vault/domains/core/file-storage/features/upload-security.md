---
domain: core
module: file-storage
feature: upload-security
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Upload Security

Parent: [[../_module]] · See [[../security]]

Validates uploads and gates file access.

- MIME + extension validation, both must agree; executables (`.php`, `.exe`) rejected.
- Per-company max file size enforced from Company Settings.
- Files exposed only via pre-signed S3 URLs (1h TTL) from `TemporaryUrlAction` — no public exposure.
- Tenant isolation: a temporary URL for company B media is not resolvable by company A *(assumed: ownership check in the action)*.

## UI

- **Kind**: background
- **Page**: background (no page) — validation is configured on each owning module's Filament Media field + per-module Data class attributes; URL issuance is `TemporaryUrlAction`. No standalone page or resource.
- **Layout**: n/a — surfaces as inline validation errors on the host form's file field, and as a resolved pre-signed link when a file is downloaded.
- **Key interactions**: user attaches a file in some domain's form → MIME/extension/size validated inline → on save the file is stored; later, requesting the file yields a 1h pre-signed S3 URL via `TemporaryUrlAction`.
- **States**: empty = no file attached · loading = upload/validation in progress · error = rejected extension (`.php`/`.exe`), MIME/extension mismatch, or over per-company max size (inline field error) · selected = an attached file shown with its (freshly signed) download link.
- **Gating**: none of its own — access rides on the owning record's module permissions; the pre-signed URL enforces tenant ownership *(assumed check in the action)* ([[../security]]).

## Data

- Owns / writes: `media` (this module's table) — stores validated file metadata; no other table.
- Reads: per-company max file size from Company Settings (read-only, [[../company-settings/_module]]); the owning model for ownership/tenancy checks (read-only).
- Cross-domain writes: none — validation and URL issuance touch only the `media` table this module owns ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none (no domain events); reads the max-file-size setting owned by [[../company-settings/_module]].
- Feeds: none directly. GDPR erasure of person-related media is driven by [[../data-privacy/erasure-cascade]] per [[../../../architecture/data-lifecycle]] — data-privacy triggers, file-storage/owning domain removes its own media.
- Shared entity: per-company max file size is reference config owned by [[../company-settings/_module]] (read-only).

## Test Checklist

### Unit
- [ ] MIME + extension must agree — a mismatch is rejected
- [ ] Disallowed extension (`.php` / `.exe`) rejected

### Feature (Pest)
- [ ] Per-company max size enforced from Company Settings — an oversize file is rejected
- [ ] `TemporaryUrlAction` issues a 1h-TTL pre-signed URL that expires after the TTL
- [ ] Tenant isolation: company A cannot resolve a temporary URL for company B media *(assumed ownership check in the action)*
