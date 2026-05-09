---
tags: [flowflex, domain/hr, recruiting, ai, phase/8]
domain: HR & People
panel: hr
color: "#7C3AED"
status: planned
last_updated: 2026-05-08
---

# AI Recruiting Assistant

AI built into the hiring workflow. Writes job descriptions, screens CVs, generates interview questions, and predicts candidate quality — so hiring managers spend time on people, not paperwork.

**Who uses it:** HR team, hiring managers, recruiters
**Filament Panel:** `hr`
**Depends on:** [[Recruitment & ATS]], [[AI Infrastructure]]
**Phase:** 8 (extension of Recruitment & ATS)
**Build complexity:** Medium — extends existing ATS with AI layer

---

## Features

### Job Description Generator

- Input: role title, department, seniority level, key responsibilities (bullet points)
- Output: full JD in FlowFlex brand voice: overview, responsibilities, requirements, nice-to-have, company culture blurb
- Tone options: formal / approachable / technical / startup
- Bias check: flags potentially exclusionary language (gendered words, unnecessary requirements)
- SEO optimisation for job board visibility
- One-click to copy to [[Recruitment & ATS]] → Job Posting form

### CV / Resume Parser & Scorer

- Upload CV (PDF/DOCX) → AI extracts structured data: name, email, phone, education, experience, skills
- Auto-fills candidate profile form
- Scores candidate against job requirements (0–100)
- Highlights matching skills (green) and missing requirements (amber)
- Red flags: employment gaps, very short tenures (highlighted, not disqualifying — human decides)
- Bulk upload: process 50 CVs at once from a ZIP file

### AI Screening Questions

- Generates role-specific screening questions based on JD
- Question types: situational, competency-based, technical, culture-fit
- Difficulty calibrated to seniority level
- Interview scorecard auto-created from generated questions
- Saves custom question banks per role type for reuse

### Candidate Summary Cards

For each candidate in pipeline:
- 1-paragraph AI summary of background and fit
- "Key strengths for this role" (3 bullet points)
- "Potential gaps to explore" (1-2 points)
- Cited from CV only — no speculation

### Salary Benchmarking Suggestions

- Based on role, level, location, and market data
- Suggests salary band aligned to current market
- Confidence indicator (high/medium/low based on data quality for that market)
- Feeds into offer letter salary suggestion

### Interview Coaching (Hiring Manager)

- Before interview: AI-generated briefing doc (candidate summary + suggested questions)
- After interview: scorecard analysis — "Your ratings suggest strong technical but weaker communication — review before deciding"
- Panel debrief helper: synthesise multiple interviewers' scorecards into combined recommendation

---

## Privacy & Bias Controls

- AI scoring is advisory only — cannot auto-reject candidates
- All AI suggestions include confidence level and source
- Bias monitoring: aggregate reports on demographics of progressed vs rejected candidates
- Audit log: every AI suggestion shown to a recruiter is recorded
- GDPR: AI-generated candidate summaries treated as personal data → deleted with candidate on retention expiry

---

## Permissions

```
hr.ai-recruiting.view
hr.ai-recruiting.generate-jd
hr.ai-recruiting.score-candidates
hr.ai-recruiting.generate-questions
hr.ai-recruiting.view-bias-report
```

---

## Competitor Comparison

| Feature | FlowFlex | Greenhouse | Lever | Ashby |
|---|---|---|---|---|
| AI JD generator | ✅ | ❌ | ❌ | ✅ |
| CV parser + scorer | ✅ | ✅ | ✅ | ✅ |
| Bias language detection | ✅ | ✅ (via plugin) | ❌ | ✅ |
| Interview question generator | ✅ | ❌ | ❌ | ✅ |
| Salary benchmarking | ✅ | ❌ | ❌ | ✅ |
| Included in base platform | ✅ | ❌ (add-on €€) | ❌ | ✅ |

---

## Related

- [[HR Overview]]
- [[Recruitment & ATS]]
- [[AI Infrastructure]]
- [[AI Assistant & Copilot]]
