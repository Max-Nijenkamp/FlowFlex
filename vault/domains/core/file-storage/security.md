---
domain: core
module: file-storage
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# File Storage — Security

Parent: [[_module]]

## Permissions

None of its own — access control rides on the owning record's module permissions. There is no standalone Filament resource; Media Library fields live inside other modules' forms.

## Upload validation

Per [[../../../security/threat-model]] and [[../../../architecture/multi-tenancy]]:

- MIME + extension validation; both must agree (mismatch rejected).
- No executables — `.php`, `.exe`, and similar are rejected.
- Size limits: per-company max file size read from Company Settings.
- Allowed families: documents (PDF, DOCX), images (JPEG, PNG, WebP), spreadsheets (XLSX, CSV).

Validation is configured on the Filament/Media Library field plus per-module Data classes (`FileTypes`, `MaxSize` attributes).

## Pre-signed URLs & tenant isolation

- Files are never publicly exposed; access is via pre-signed S3 URLs with a 1h TTL issued by `TemporaryUrlAction`.
- Every stored path is prefixed `companies/{company_id}/...` by `CompanyPathGenerator`, so tenant A cannot address tenant B's objects. Company A must not be able to resolve a temporary URL for company B media *(assumed: ownership check in the action)*. See [[../../../security/tenancy-isolation]].

## GDPR

Files ABOUT a person are deleted on erasure per [[../../../architecture/data-lifecycle]] — the erasure cascade in [[../data-privacy/_module]] includes media owned by anonymised records. See [[../../../security/encryption]].
