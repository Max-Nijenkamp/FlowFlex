---
tags: [flowflex, domain/hr, recruitment, ats, hiring, phase/8]
domain: HR & People
panel: hr
color: "#7C3AED"
status: planned
last_updated: 2026-05-06
---

# Recruitment & ATS

Full applicant tracking system. From job requisition to signed offer letter, with automatic hand-off to onboarding on hire.

**Who uses it:** HR team, hiring managers, recruiters
**Filament Panel:** `hr`
**Depends on:** [[Employee Profiles]] (for creating profile on hire)
**Phase:** 8
**Build complexity:** Very High — 5 resources, 3 pages, 10 tables

## Events Fired

- `JobPostingPublished`
- `ApplicationReceived`
- `CandidateAdvanced` (moved to next pipeline stage)
- `OfferMade`
- `CandidateHired` → consumed by [[Employee Profiles]] (create record), [[Onboarding]] (start flow)
- `CandidateRejected`

## Sub-modules

### Job Requisitions & Postings

- Job requisition form (department, role, salary band, start date, justification)
- Approval workflow for new headcount (manager → finance → HR director)
- Job posting builder (title, description, requirements, salary range optional)
- Careers page embed (iframe snippet or hosted `/careers` subdomain)
- Multi-channel job publishing: Indeed, LinkedIn, Glassdoor (via API integrations)
- Internal job board (post internally first, before external)

### Application Pipeline

- Kanban pipeline per job (custom stages: Applied → Screening → Interview 1 → Interview 2 → Offer → Hired)
- Bulk actions (move multiple candidates to next stage, reject with template email)
- Application source tracking (where did the candidate come from?)
- Resume/CV storage and parsing (auto-fill candidate details from CV upload)
- Candidate profile (full history, all applications to the company)
- Duplicate detection (same candidate applying to multiple roles)
- GDPR-compliant candidate data retention and deletion

### Interviews & Scorecards

- Interview scheduling (suggest times from interviewer's calendar, send invite)
- Scorecard builder (define rating criteria per role, per interview stage)
- Interviewer scorecard submission
- Panel interview coordination (multiple interviewers, aggregate scores)
- Interview feedback compilation view (hiring manager sees all scores)
- Candidate interview confirmation and reminder emails

### Offers & Closing

- Offer letter generator (template with merge fields: name, role, salary, start date)
- Offer approval workflow (HR director sign-off on comp above threshold)
- E-signature on offer letter (candidate signs digitally)
- Offer status tracking: sent / viewed / signed / declined
- Counter-offer handling notes
- Rejection templates (multiple templates for different rejection reasons)

### Referral Programme

- Employee referral submission form
- Referral tracking (who referred whom, for which role)
- Referral bonus configuration (amount, trigger: interview / hire / 3-month anniversary)
- Referral dashboard (top referrers, referral conversion rate)
- Automated payout trigger to [[Payroll]] module on bonus eligibility

## Database Tables (10)

1. `job_requisitions` — headcount approval and job brief
2. `job_postings` — public-facing job post content and status
3. `candidates` — candidate profiles (central across all applications)
4. `applications` — per-candidate-per-job application record
5. `application_pipeline_stages` — stage definitions per job
6. `interviews` — scheduled interview records
7. `interview_scorecards` — per-interviewer scoring
8. `offers` — offer details and status
9. `referrals` — employee referral records
10. `candidate_emails` — email sequences and sent history

## Related

- [[HR Overview]]
- [[Employee Profiles]]
- [[Onboarding]]
- [[Payroll]]
