---
domain: legal
module: policy-library
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Policy Library — Security

## Access contract

`canAccess() = Auth::user()->can('legal.policies.view-any') && BillingService::hasModule('legal.policies')` per [[../../../architecture/filament-patterns]] #1. Self-service pages (`MyPoliciesPage`) gate on `legal.policies.acknowledge-own` (all employees).

## Acknowledge own only

`AcknowledgePolicyAction` writes an ack for the actor's own employee record only — no acknowledging on behalf of others.

## Content safety

Policy `body` is purified with `ezyang/htmlpurifier` on write (XSS prevention on rich text).

## Permissions

`legal.policies.view-any` · `legal.policies.create` · `legal.policies.publish` · `legal.policies.acknowledge-own` (all employees)

## Data ownership

Writes only `legal_policies`, `legal_policy_acknowledgements`; employee/department + control data read-only; reminders via `core.notifications` ([[../../../security/data-ownership]]).
