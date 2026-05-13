---
type: module
domain: Legal & Compliance
panel: legal
module-key: legal.document-review
status: planned
color: "#4ADE80"
---

# Document Review

> Structured legal document review workflow from draft through multiple review rounds and final approval before execution.

**Panel:** `legal`
**Module key:** `legal.document-review`

## What It Does

Document Review manages the collaborative review and approval of legal documents before they are executed. A drafter uploads or creates a document, assigns reviewers in the required sequence, and each reviewer receives a task to read, comment, and approve or request changes. Comments are threaded to specific sections. Once all reviewers approve, the document moves to the approver for final sign-off and then to [[e-signatures]] for execution. The full review history — who reviewed what version, what changes were requested, and who approved — is preserved for compliance.

## Features

### Core
- Document submission: upload a draft PDF or Word document; link to a contract record in [[contracts]]
- Review assignment: designate one or more reviewers in sequence or in parallel; each assigned a review deadline
- Reviewer task: reviewer receives a notification with the document link; can comment on the document and approve or request changes
- Section-level comments: reviewers annotate specific clauses or paragraphs; comments visible to all reviewers
- Version management: when the drafter uploads a revised version after feedback, prior versions are preserved with their comment threads
- Status workflow: draft → under review → revision needed → approved → ready for signing

### Advanced
- Parallel vs sequential review: configure whether all reviewers review simultaneously or in a defined sequence
- Review deadline tracking: alert drafter when a reviewer has not responded within their deadline
- Approval gate: designated approver (e.g., General Counsel) must give final approval before signing stage
- Comment resolution tracking: each comment is either resolved by the drafter or marked as accepted; approval blocked until all comments are resolved
- Template-based documents: start a review from a standard template (NDA, service agreement, amendment) to ensure consistent starting point
- Review metrics: average review cycle time per document type; identify bottlenecks in the review process

### AI-Powered
- Clause risk highlight: AI flags clauses that deviate from the clause library or contain non-standard provisions before review begins
- Suggested changes: based on past reviewer comments on similar documents, suggest likely revision points for the drafter to address proactively

## Data Model

```erDiagram
    legal_review_documents {
        ulid id PK
        ulid company_id FK
        ulid contract_id FK
        string title
        string document_type
        string status
        ulid drafter_id FK
        ulid approver_id FK
        timestamp approved_at
        timestamps timestamps
        softDeletes deleted_at
    }

    legal_review_assignments {
        ulid id PK
        ulid document_id FK
        ulid reviewer_id FK
        integer review_order
        string status
        date deadline
        timestamp completed_at
    }

    legal_review_comments {
        ulid id PK
        ulid document_id FK
        ulid reviewer_id FK
        string section_reference
        text comment
        string status
        ulid resolved_by FK
        timestamp resolved_at
        timestamps timestamps
    }

    legal_review_documents ||--o{ legal_review_assignments : "reviewed by"
    legal_review_documents ||--o{ legal_review_comments : "has"
```

| Table | Purpose |
|---|---|
| `legal_review_documents` | Document under review with status and approver |
| `legal_review_assignments` | Per-reviewer tasks with deadline and completion |
| `legal_review_comments` | Threaded comments with resolution tracking |

## Permissions

```
legal.document-review.view-any
legal.document-review.create
legal.document-review.review
legal.document-review.approve
legal.document-review.delete
```

## Filament

**Resource class:** `ReviewDocumentResource`
**Pages:** List, Create, View
**Custom pages:** `DocumentReviewWorkspacePage` (reviewer interface with comment sidebar and version diff)
**Widgets:** `PendingReviewsWidget` (documents awaiting the current user's review)
**Nav group:** Contracts

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Ironclad Workflow | Legal document review and approval workflow |
| ContractPodAi Review | AI-assisted document review |
| NetDocuments | Legal document management and review |
| iManage Work | Matter-centric document review |

## Implementation Notes

**Filament:** `DocumentReviewWorkspacePage` is a custom `Page` — the reviewer interface with comment sidebar and version diff cannot be built with standard Filament forms. The page layout is a two-panel view: left panel renders the PDF (using PDF.js) and right panel lists comments anchored to `section_reference` strings. Clicking a comment highlights the referenced section in the PDF by scrolling PDF.js to the page and highlighting a text span (requires PDF.js text layer rendering).

**Comment anchoring:** `legal_review_comments.section_reference` stores a reference like `"Section 4.2"` or a PDF text selection (start char offset + end char offset). For MVP, use simple text labels that the reviewer types manually. For Phase 2, implement PDF text selection → auto-populated section reference using PDF.js `getTextContent()`.

**Version management:** When the drafter uploads a revised document, the prior file URL is preserved in a `legal_review_document_versions {ulid id, ulid document_id, integer version_number, string file_url, ulid uploaded_by, timestamp created_at}` table — this table is not in the current data model. Add it. The "version diff" in `DocumentReviewWorkspacePage` compares two PDF versions — for MVP, show version history as a list of version cards; a full diff overlay (PDF page comparison) is a Phase 2 feature.

**Review notifications:** When a reviewer's turn arrives (sequential mode) or when the document is submitted (parallel mode), `ReviewAssignedNotification` is dispatched via the notifications module. When all assignments are complete, `ReviewCompleteNotification` is sent to the drafter.

**AI features:** Clause risk highlight runs when a document is submitted for review — `app/Services/AI/ContractRiskService.php` sends the extracted PDF text (from the OCR/document-intelligence module) to OpenAI GPT-4o with a prompt comparing clauses against a clause library. The response JSON `{clause, risk_level, explanation}` is stored in `legal_review_comments` as AI-generated comments with `reviewer_id = null` (system comments). Reviewers can resolve or keep these AI flags.

**Missing from data model:** `legal_review_documents.document_type` should be an enum: `nda | service_agreement | employment_contract | settlement | other` — needed for the template-based document feature and for the AI clause library matching.

## Related

- [[contracts]] — reviewed documents linked to contract records
- [[e-signatures]] — approved documents sent for signing
- [[matter-management]] — matter documents reviewed here
