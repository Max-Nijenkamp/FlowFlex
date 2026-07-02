---
domain: core
module: file-storage
feature: path-generator
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Path Generator

Parent: [[../_module]] · See [[../architecture]]

Enforces tenant-segregated storage paths for all media.

- `CompanyPathGenerator` implements the Media Library `PathGenerator`, bound in `config/media-library.php`.
- Every path — originals, conversions, responsive images — is prefixed `companies/{company_id}/{table}/{model_id}/{filename}`.
- `FileStorageService::pathFor($model, $filename)` is the single construction point; no domain calls raw `Storage::put()`.
- Test: every stored file path starts with `companies/{company_id}/`, including conversions.

## UI

- **Kind**: background
- **Page**: background (no page) — the path generator is infrastructure bound in `config/media-library.php`; it has no screen. It runs whenever any domain's Filament Media field / `addMedia()` stores a file.
- **Layout**: n/a. Trigger: every media store (original, conversion, responsive image) routes through `CompanyPathGenerator` / `FileStorageService::pathFor()`.
- **Key interactions**: none directly — transparent to users; they upload via the owning module's form and the prefix is applied automatically.
- **States**: empty = no media stored yet · loading = n/a (synchronous path build) · error = missing `company_id` in context would be a hard failure *(assumed: fail-closed, no unscoped write)* · selected = n/a.
- **Gating**: none of its own — the upload inherits the owning record's module permissions ([[../security]]).

## Data

- Owns / writes: the physical storage layout under `companies/{company_id}/...` and the `media` table's path-related columns (this module's table). Writes only its own media rows/files.
- Reads: `CompanyContext` for `company_id`; the owning model's table/id to build the path (read-only).
- Cross-domain writes: none — it stores files/media rows on behalf of any owner model but writes only the `media` table it owns ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none (no domain events) — invoked in-process by any domain's media store.
- Feeds: none.
- Shared entity: the owner model (polymorphic `model_type`/`model_id`) is owned by another domain; referenced read-only to build the path, never written.
