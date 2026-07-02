---
domain: communications
module: sms-channel
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# SMS Channel — Decisions

## ADR: Provider abstraction, Twilio first (source, assumed)

- **Decision:** `SmsDriver` abstracts Twilio vs Vonage; Twilio is the assumed first implementation *(assumed)*.
- **Consequences:** `provider` column drives normalise/send; adding Vonage is additive.

## ADR: Opt-out enforced platform-wide via one service (source)

- **Context:** STOP compliance must hold across inbox sends and broadcasts.
- **Decision:** A single `OptOutService::isOptedOut` is the source of truth; the driver throws `RecipientOptedOutException`, and broadcast materialisation excludes opted-out numbers.
- **Consequences:** No side-door that skips opt-out. Regulatory control (see [[security]]).

## ADR: Cost tracked in message meta (source, assumed)

- **Decision:** Per-message cost from provider callbacks is stored in `comms_messages.meta.cost_cents` *(assumed jsonb meta column on the inbox-owned table)*, using `brick/money`.
- **Consequences:** Depends on the inbox `meta` column existing. See [[unknowns]].

## ADR: Message rows owned by the inbox (data-ownership)

- **Decision:** SMS never writes `comms_messages`; normalised inbound goes to `InboxService`, which writes the row.

## Related

- [[_module]] · [[architecture]] · [[../../../security/data-ownership]]
