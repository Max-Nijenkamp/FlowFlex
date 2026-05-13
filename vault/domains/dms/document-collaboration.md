---
type: module
domain: Document Management
panel: dms
module-key: dms.collaboration
status: planned
color: "#4ADE80"
---

# Document Collaboration

> Real-time document co-editing, inline commenting, review requests, and approval sign-off.

**Panel:** `dms`
**Module key:** `dms.collaboration`

---

## What It Does

Document Collaboration enables multiple people to work on the same document simultaneously with real-time co-editing, similar to Google Docs. Team members can leave inline comments on specific sections, tag colleagues for their input, and request a formal review. Reviewers can approve, reject, or request changes, creating a documented audit trail of who reviewed what and when. Approved documents can be locked to prevent further edits, preserving the approved version.

---

## Features

### Core
- Real-time co-editing: multiple users editing a document simultaneously with live cursor tracking
- Inline comments: highlight text and leave a comment; thread replies supported
- Mention tagging: @mention a colleague in a comment to notify them
- Review request: formally request a review from one or more colleagues
- Reviewer decisions: reviewers can approve, reject, or request changes with a written reason
- Approval lock: approved document locked; further edits require a new version

### Advanced
- Version comparison: side-by-side comparison of two document versions with change highlighting
- Comment resolution: mark comments as resolved when addressed; resolved comments collapsed by default
- Guest access: invite external reviewers with a time-limited link without requiring a FlowFlex account
- Activity feed: per-document log of all edits, comments, and review actions with timestamps
- Notification preferences: configure which collaboration events trigger notifications

### AI-Powered
- Comment summarisation: AI summarises a long comment thread to the key unresolved point
- Grammar and style check: AI highlights grammar issues or style inconsistencies in document content
- Review readiness check: AI scans the document for completeness before a review is submitted

---

## Data Model

```erDiagram
    document_comments {
        ulid id PK
        ulid document_id FK
        ulid author_id FK
        ulid company_id FK
        string anchor_text
        text body
        boolean is_resolved
        ulid parent_id FK
        timestamps created_at_updated_at
    }

    document_reviews {
        ulid id PK
        ulid document_id FK
        ulid requested_by FK
        ulid reviewer_id FK
        ulid company_id FK
        string status
        text decision_notes
        timestamp decided_at
        timestamps created_at_updated_at
    }

    dms_documents ||--o{ document_comments : "has"
    dms_documents ||--o{ document_reviews : "reviewed via"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `document_comments` | Inline comments | `id`, `document_id`, `author_id`, `anchor_text`, `body`, `is_resolved` |
| `document_reviews` | Formal reviews | `id`, `document_id`, `reviewer_id`, `status`, `decision_notes`, `decided_at` |

---

## Permissions

```
dms.collaboration.comment
dms.collaboration.review
dms.collaboration.approve
dms.collaboration.manage-guest-access
dms.collaboration.lock-document
```

---

## Filament

- **Resource:** `App\Filament\Dms\Resources\DocumentReviewResource`
- **Pages:** `ListDocumentReviews`, `ViewDocumentReview`
- **Custom pages:** `DocumentEditorPage` (real-time editor), `ReviewQueuePage`
- **Widgets:** `PendingReviewsWidget`, `OpenCommentsWidget`
- **Nav group:** Library

---

## Displaces

| Feature | FlowFlex | Google Docs | Confluence | SharePoint |
|---|---|---|---|---|
| Real-time co-editing | Yes | Yes | Yes | Yes |
| Formal review workflow | Yes | No | No | Yes |
| Guest review access | Yes | Yes | No | Partial |
| AI comment summarisation | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Implementation Notes

**Real-time co-editing — highest technical complexity in the DMS domain:** Real-time collaborative editing requires Operational Transformation (OT) or Conflict-free Replicated Data Types (CRDTs). This is non-trivial to implement from scratch. Two viable approaches:

1. **Tiptap Collaboration (Y.js based):** Tiptap (already in the tech stack for rich text) has a collaboration extension built on **Y.js** (CRDT library). Requires a **Y.js WebSocket server** (`@y-websocket/server` node package) to synchronise document state between concurrent editors. This server can run as a separate Node.js process — add it to `docker-compose.yml`. Document state (Y.js doc binary) is persisted in Redis and snapshotted periodically to the database. **Recommended for MVP** — leverages the existing Tiptap investment.

2. **ProseMirror + custom OT:** Build a custom OT engine. Much higher complexity — not recommended.

**Tiptap Collaboration implementation:** The `DocumentEditorPage` custom Filament `Page` loads the Tiptap editor with the `Collaboration` and `CollaborationCursor` extensions. Each user's cursor position is shared via the Y.js WebSocket server. Document content changes are broadcast in real time. On page load, the editor reconnects to the Y.js doc for the document ID. Persistence: Y.js updates are stored in Redis; a `PersistDocumentJob` saves the final state to `dms_documents.content` every 60 seconds and on the last user leaving.

**Real-time (Reverb):** Reverb is used for awareness notifications (who is currently editing, comment posted, review requested) — NOT for the document sync itself (which uses the Y.js WebSocket server). `DocumentCommentPosted` event broadcasts on `dms.document.{document_id}` channel. Livewire listens to refresh the comment sidebar without a full reload.

**Tiptap and `anchor_text` for inline comments:** `document_comments.anchor_text` stores the highlighted text that the comment is anchored to. Tiptap can mark this anchor with a custom decoration (a mark type `comment` with the comment ID). This allows the editor to highlight commented text and scroll to the anchor when a comment is selected.

**AI features:** Comment summarisation and grammar/style check both call `app/Services/AI/DocumentAiService.php` wrapping OpenAI GPT-4o. These run on-demand (button click) — not automatically. Review readiness check is also on-demand, triggered from the "Request review" action — it sends the document content to GPT-4o with a checklist of expected sections for the document type.

**Missing from data model:** `document_comments.anchor_text` alone is not sufficient for re-anchoring comments after document edits. Add `anchor_from_pos integer nullable` and `anchor_to_pos integer nullable` (Y.js document positions) for more robust comment anchoring. Also, the approval lock mechanism requires a `dms_documents.locked_at timestamp nullable` and `locked_by ulid nullable FK` — not in the current data model.

## Related

- [[document-library]] — collaboration operates on documents in the library
- [[document-workflows]] — review/approval integrated with workflow engine
- [[document-retention]] — approved document subject to retention policy
