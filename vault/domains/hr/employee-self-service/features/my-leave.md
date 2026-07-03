---
domain: hr
module: employee-self-service
feature: my-leave
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# My Leave

**Purpose.** Let the employee submit leave requests and view their own balance and request history.

**Behavior.** Leave tile on `SelfServiceDashboardPage` shows balance; submission + history scoped to the auth employee. Tile and functionality render only when `hr.leave` is active; hidden otherwise (soft-dep degraded behavior).

**Source module.** [[../../leave-management/_module]] (soft dependency)

**Permissions.** `hr.self-service.view`.

## UI

- **Kind**: custom-page (soft-dep hr.leave — page hidden when hr.leave inactive)
- **Page**: "My Leave" (`/app/my-leave`)
- **Layout**: leave balance summary + submit-leave form + own leave request history table.
- **Key interactions**: view balance; submit a leave request; browse own request history; open a request row for detail.
- **States**: empty = "No leave requests yet"; loading = skeleton; error = overlapping-request or insufficient-balance rejection; selected = request row detail.
- **Gating**: visible with `hr.self-service.access` AND hr.leave active; submitting a request requires the leave submit permission *(assumed, owned by hr.leave)*.

  > [!warning] UNVERIFIED
  > Page/tile is hidden entirely when the hr.leave module is inactive (soft-dep degraded behavior). Exact hidden-vs-disabled behavior unconfirmed.

## Data

- Owns / writes: none — this module owns no tables.
- Reads: leave balances + requests (owned by hr.leave) scoped to own employee, via hr.leave's service.
- Cross-domain writes: leave submission goes through hr.leave's service (never a direct write — [[../../../../security/data-ownership]]).

## Relations

- Consumes: none (renders hr.leave data live via service).
- Feeds: leave submission handled by hr.leave (no own event fired).
- Shared entity: reads hr.leave balances / requests.

## Test Checklist

### Unit
- [ ] The leave tile/page renders only when `hr.leave` is active (soft-dep gating)

### Feature (Pest)
- [ ] Balance + request history are scoped to `auth()->user()->employee`
- [ ] Submitting a request routes through hr.leave's service (never a direct write)
- [ ] Self-scope isolation: employee A cannot see employee B's balance or requests

### Livewire
- [ ] Page/tile hidden when `hr.leave` inactive; submit action gated on the hr.leave submit permission

[[../_module]]
