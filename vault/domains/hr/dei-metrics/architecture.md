---
domain: hr
module: dei-metrics
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# DEI Metrics — Architecture

Intended service/action layout and the anonymized snapshot pipeline. See [[_module]].

## Services & Actions

| Class | Responsibility |
|---|---|
| `DeiSnapshotService::generate(string $period): void` | Decrypts the attribute set **inside a job**, aggregates, suppresses groups < N, stores the snapshot, discards individuals |
| `SubmitOwnDeiAttributesAction::run(SubmitDeiAttributesData $data): void` | Own-only submission; writes consent log |
| `WithdrawDeiConsentAction::run(): void` | Deletes own attributes + logs withdrawal |

## Jobs & Scheduling

Folded from the source spec's Jobs & Scheduling section.

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `GenerateDeiSnapshotsCommand` | hr | quarterly | upsert per `(company, period, dimension)` |

Runs on the `hr` queue via [[../../../infrastructure/queue-horizon]]. Idempotent by upsert on `(company, period, dimension)` — safe to re-run a period.

## Snapshot Pipeline

```mermaid
flowchart TD
  A["Employee self-declares<br/>(opt-in + consent checkbox)"] --> B["SubmitOwnDeiAttributesAction<br/>writes consent log via core.privacy"]
  B --> C["hr_dei_attributes<br/>value ENCRYPTED at rest"]
  C -. "quarterly" .-> D["GenerateDeiSnapshotsCommand<br/>(hr queue)"]
  D --> E["DeiSnapshotService::generate(period)"]
  E --> F["Decrypt attribute set IN JOB"]
  F --> G["Aggregate counts by dimension"]
  G --> H{"Group size >= N?"}
  H -- "no" --> I["Suppress group"]
  H -- "yes" --> J["Keep count"]
  I --> K["hr_dei_snapshots.breakdown (jsonb)<br/>aggregated, suppressed"]
  J --> K
  E --> L["Discard decrypted individuals"]
  K --> M["DeiDashboardPage reads<br/>snapshots only"]
```

Dashboards **never** live decrypt-and-group over individuals at request time — they read pre-computed snapshots. Withdrawal of consent deletes the source row before the next snapshot; already-stored snapshots hold aggregates only.

## Related

- [[data-model]]
- [[security]]
- [[../../../architecture/patterns/custom-pages]] (`DeiDashboardPage`)
- [[../../../architecture/patterns/encryption]]
- [[../../../infrastructure/queue-horizon]]
