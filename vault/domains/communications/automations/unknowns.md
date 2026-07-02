---
domain: communications
module: automations
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Automations — Unknowns

## Assumed Items

- Conditions are AND-combined *(assumed)*.
- Away message fires once per conversation per day *(assumed)*.
- One active chatbot flow per channel *(assumed)*.
- Chatbot flow position stored in `comms_conversations` meta *(assumed jsonb, inbox-owned)*.
- No separate execution-log table — counters + activitylog *(assumed)*.

## Open Questions

> [!warning] UNVERIFIED
> Whether the inbox should fire an inbound **bus event** (instead of the in-process engine call) is undecided — an event would decouple automations but needs an event contract. See [[decisions]].

- OR / nested condition logic beyond flat AND?
- Chatbot flow-position column: does the inbox `meta` column exist, or does automations need its own state table?
- Escalation action target (which agent/team, round-robin?).
- Interaction between chatbot flow and rules when both could match.

## Related

- [[_module]] · [[decisions]]
