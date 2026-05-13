---
type: module
domain: HR & People
panel: hr
module-key: hr.recruitment
status: planned
color: "#4ADE80"
---

# Recruitment

> Job requisitions, applicant tracking, interview stages, scorecards, and offer management — the full hiring funnel from open role to signed offer.

**Panel:** `hr`
**Module key:** `hr.recruitment`

## What It Does

Recruitment manages the end-to-end hiring process inside FlowFlex. HR or a hiring manager creates a job requisition. Candidates apply (via a public application form or manual entry) and move through configurable interview stages. Interviewers complete scorecards after each stage. When a candidate is selected, an offer is issued. On offer acceptance, the candidate converts to an employee record in Employee Profiles. The module feeds headcount data into Workforce Planning and time-to-hire into HR Analytics.

## Features

### Core
- Job requisitions: title, department, location, employment type, headcount target, job description, open/close dates
- Applicant tracking: candidates move through pipeline stages (e.g. Applied → Screening → Interview → Offer → Hired/Rejected)
- Configurable stages: company defines their own stage names and order per requisition or globally
- Kanban view of applicants per requisition: drag cards between stages
- Manual candidate entry: add candidates directly (e.g. from referrals or LinkedIn outreach)

### Advanced
- Application form: public-facing form (shareable URL) for candidates to apply — collects CV, cover letter, contact info
- Interview scheduling: create interview events linked to a candidate; interviewers notified via notification module
- Scorecards: interviewers complete a structured scorecard after each interview round — ratings and comments collated per candidate
- Offer management: generate offer letter PDF from template; track offer status (sent/accepted/declined/expired)
- Conversion: on offer acceptance, one-click convert candidate to employee record in Employee Profiles with hire date pre-filled

### AI-Powered
- CV parsing: uploaded CVs parsed and key information (skills, experience, education) extracted and mapped to candidate profile fields automatically
- Candidate ranking: AI scores candidates against the job description requirements — hiring manager sees a ranked shortlist with explanation

## Data Model

```erDiagram
    job_requisitions {
        ulid id PK
        ulid company_id FK
        string title
        ulid department_id FK
        string employment_type
        integer headcount
        text description
        string status
        date open_date
        date close_date
        ulid hiring_manager_id FK
        timestamps created_at/updated_at
    }

    applicants {
        ulid id PK
        ulid company_id FK
        ulid requisition_id FK
        string first_name
        string last_name
        string email
        string phone
        string current_stage
        string status
        string cv_path
        json parsed_cv
        decimal ai_score
        timestamps created_at/updated_at
    }

    interview_scorecards {
        ulid id PK
        ulid applicant_id FK
        ulid interviewer_id FK
        string stage
        integer rating
        text strengths
        text concerns
        string recommendation
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `status` | active / on_hold / closed / filled |
| `applicants.status` | active / hired / rejected / withdrawn |
| `recommendation` | strong_yes / yes / no / strong_no |

## Permissions

- `hr.recruitment.view`
- `hr.recruitment.manage-requisitions`
- `hr.recruitment.manage-applicants`
- `hr.recruitment.complete-scorecard`
- `hr.recruitment.make-offer`

## Filament

- **Resource:** `JobRequisitionResource`, `ApplicantResource`
- **Pages:** `ListJobRequisitions`, `ViewRequisition` (with applicant pipeline kanban)
- **Custom pages:** `ApplicantPipelinePage` — kanban view of candidates per requisition stage
- **Widgets:** `OpenRolesWidget` — count of active requisitions on HR dashboard
- **Nav group:** Employees (hr panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Greenhouse | Applicant tracking system |
| Lever | Recruiting and applicant management |
| Workable | Job posting and candidate tracking |
| BambooHR Applicant Tracking | HR applicant tracking |

## Implementation Notes

**Filament:** `ApplicantPipelinePage` is a custom `Page` — a kanban view of applicants per stage for a specific requisition. Architecturally identical to `KanbanBoardPage` (projects) and `SalesPipelinePage` (CRM). Dragging a candidate card between stage columns updates `applicants.current_stage` via Livewire action `moveApplicant($applicantId, $newStage)`. The page receives the requisition ID from the URL parameter (e.g. `/hr/applicant-pipeline/{requisition_id}`).

**`ViewRequisition`** page uses Filament `Tabs`: Tab 1 = Requisition details, Tab 2 = Applicants pipeline (embedded `ApplicantPipelinePage` or a Livewire component), Tab 3 = Scorecards summary (aggregate ratings per stage), Tab 4 = Offer status.

**Public application form:** The candidate application form at the shareable URL is NOT a Filament page — it is a public Vue 3 + Inertia page (per the tech stack decision table: "Checkout and booking flows — Vue 3 + Inertia" applies here too). The route `GET /apply/{requisition_slug}` renders the application form; `POST /apply/{requisition_slug}` creates an `applicants` record. CV file upload goes to S3 via `spatie/laravel-media-library` with a temporary signed URL.

**CV parsing (AI):** `applicants.parsed_cv` is populated by `ParseCvJob` dispatched after the CV file is uploaded. The job extracts text from the PDF via the OCR module (or directly via `smalot/pdfparser` for native PDF text extraction, add to `composer.json`), then sends the text to OpenAI GPT-4o with a structured output prompt requesting `{skills: [], experience: [{company, role, years}], education: [{degree, institution}]}`.

**Candidate ranking (AI):** `UpdateApplicantScoreJob` is dispatched after CV parsing completes. It sends the parsed CV JSON and the job description to OpenAI GPT-4o requesting a score (0–100) and a one-sentence explanation. Scores are stored in `applicants.ai_score`. The hiring manager sees applicants sorted by `ai_score` descending with the explanation shown on hover.

**Offer letter PDF:** Generated using `barryvdh/laravel-dompdf` from a Blade template at `resources/views/recruitment/offer-letter.blade.php`. Triggered from a Filament `Action` button on the applicant view. The PDF is attached to an offer letter email sent to the candidate and stored in `spatie/laravel-media-library` on the applicant record.

**Conversion to employee:** The "Convert to employee" Filament action on the accepted offer creates an `employees` record populated from the `applicants` record. The `employee_id` is then stored on the `applicants` row (`ulid employee_id FK nullable` — add this column to the data model) so the applicant record links to the resulting employee.

## Related

- [[employee-profiles]]
- [[onboarding]]
- [[workforce-planning]]
- [[hr-analytics]]
