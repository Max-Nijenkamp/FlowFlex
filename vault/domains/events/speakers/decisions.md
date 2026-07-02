---
domain: events
module: speakers
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Speakers — Decisions

## ADR: Reusable directory, not per-event records

- **Context:** Speakers often recur across a company's events.
- **Decision:** `ev_speakers` is a company-level directory; assignment to a specific event happens through `ev_session_speakers` against sessions.
- **Consequences:** One profile, many assignments; edits propagate.

## ADR: Signed-token self-submit instead of a speaker portal

- **Context:** Speakers need to supply their own bio/photo without a full account.
- **Decision:** A signed `submit_token` grants a scoped, rate-limited public form (replaces a "portal") *(assumed)*.
- **Consequences:** No auth overhead for speakers; token must be signed + expiring, uploads sanitized.

## ADR: Confirmed-only public visibility

- **Context:** Invited-but-unconfirmed speakers should not appear publicly.
- **Decision:** The landing renders only `confirmed` assignments; `invited`/`declined` stay internal. Logistics is never public.
- **Consequences:** Clear separation between working state and published state.
