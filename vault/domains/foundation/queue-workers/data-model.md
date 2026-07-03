---
domain: foundation
module: queue-workers
type: data-model
color: "#4ADE80"
updated: 2026-07-03
---

# Queue Workers — Data Model

This module owns only the Laravel-standard queue tables — no custom columns.

### jobs

Laravel-standard (`queue:table` migration). **Indexes:** framework defaults.

### failed_jobs

Laravel-standard. **Indexes:** framework defaults.

### job_batches

Laravel-standard. **Indexes:** framework defaults.

## Related

- [[_module|Hub]] · [[architecture]] · [[security]]
