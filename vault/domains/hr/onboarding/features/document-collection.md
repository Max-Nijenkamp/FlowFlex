---
domain: hr
module: onboarding
feature: document-collection
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Document Collection

Part of [[../_module]].

## Purpose

Request signed documents from a new hire (contract, ID, tax forms) as part of onboarding.

## Behavior

- Represented as onboarding tasks (typically `assigned_role = employee`) within a plan.
- HR requests; employee provides via self-service when active, else HR records on behalf.

## Tables / Permissions / Events

- Tables: `hr_onboarding_tasks`, `hr_onboarding_plan_tasks`.
- Permissions: `hr.onboarding.complete-task`, `hr.onboarding.view`.
- Events: none.

## UI

- **Kind**: custom-page (collect-required-documents checklist within a plan)
- **Page**: "Document Collection" (`/hr/onboarding/{plan}/documents`)
- **Layout**: checklist of required onboarding documents (contract, ID, tax forms) with upload/collected status via Media Library.
- **Key interactions**: upload a document or mark it collected.
- **States**: empty = "No documents required" · loading = upload progress · error = rejected file type/size · selected = view collected doc.
- **Gating**: visible with `hr.onboarding.view`; mark collected/upload requires `hr.onboarding.update` *(assumed)*.

> [!warning] UNVERIFIED
> Whether documents are stored via core.files Media Library on the plan or on the employee record is not confirmed.

## Data

- Owns / writes: `hr_onboarding_plan_tasks` (document task status); documents via core.files Media Library.
- Reads: core.files.
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]]).

## Relations

- Consumes: none.
- Feeds: none.
- Shared entity: reads core.files Media Library.

> UNVERIFIED: no dedicated document table or signed-status tracking is defined in the spec — see [[../unknowns]].
