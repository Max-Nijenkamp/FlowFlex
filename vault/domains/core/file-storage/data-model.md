---
domain: core
module: file-storage
type: data-model
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# File Storage — Data Model

Parent: [[_module]] · See also [[architecture]] · [[security]]

No tables of its own beyond the published Spatie `media` table. The migration is the standard `spatie/laravel-media-library` schema with one addition: a `company_id` column, indexed, for tenant scoping.

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK (standard Media Library) |
| company_id | ulid | not null, indexed — added for tenancy |
| model_type / model_id | morphs | polymorphic owner |
| collection_name, file_name, mime_type, size, ... | — | standard Spatie columns |

Physical storage layout is enforced by `CompanyPathGenerator`, not by the schema: `companies/{company_id}/{table}/{model_id}/{filename}`. See [[architecture]].
