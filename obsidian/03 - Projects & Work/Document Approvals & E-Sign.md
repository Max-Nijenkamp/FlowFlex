---
tags: [flowflex, domain/projects, approvals, e-signature, phase/5]
domain: Projects & Work
panel: projects
color: "#4F46E5"
status: planned
last_updated: 2026-05-06
---

# Document Approvals & E-Sign

Formal approval workflows for any document. Built-in e-signature so you never need DocuSign.

**Who uses it:** All employees (requesters), managers, legal, HR
**Filament Panel:** `projects`
**Depends on:** [[Document Management]]
**Phase:** 5
**Build complexity:** High — 2 resources, 2 pages, 5 tables

## Events Fired

- `ApprovalRequested`
- `ApprovalCompleted` → triggers relevant downstream action
- `DocumentSigned`
- `ApprovalRejected`

## Features

- **Approval workflow builder** — drag-and-drop step editor
- **Sequential approvals** — A must approve before B sees it
- **Parallel approvals** — A and B can approve simultaneously, both required
- **Optional approvers** — one of these people must approve
- **Role-based approvers** — e.g. "someone with Finance Manager role"
- **Rejection flow** — rejected document goes back to originator with reason
- **Revision cycle** — submitter edits and resubmits; approval chain restarts or continues from rejection point
- **Deadline per step** — auto-escalate if not actioned within N hours
- **E-signature fields** — drag-and-drop signature boxes onto PDF
- **Audit trail** — every view, every action, timestamped, with IP
- **Signed document stored automatically** in [[Document Management]]

## E-Signature Details

Built-in e-signature is legally binding (equivalent to DocuSign). No external tool required.

The signature process:
1. Requester places signature field on PDF
2. Signer receives email with secure link
3. Signer reviews document, draws/types signature
4. Signature is embedded and the PDF is locked
5. Audit certificate generated (who signed, IP, timestamp, document hash)

## Database Tables (5)

1. `approval_workflows` — workflow definitions
2. `approval_workflow_steps` — step definitions per workflow
3. `approval_requests` — active approval instances
4. `approval_actions` — per-step approval/rejection records
5. `e_signatures` — signature records linked to documents

## Related

- [[Projects Overview]]
- [[Document Management]]
- [[Contract Management]]
- [[Onboarding]]
