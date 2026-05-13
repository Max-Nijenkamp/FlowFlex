---
type: module
domain: Learning & Development
panel: lms
cssclasses: domain-lms
phase: 7
status: complete
migration_range: 480013
last_updated: 2026-05-12
right_brain_log: "[[builder-log-lms-phase7]]"
---

# SCORM / xAPI / AICC Support

Import and play industry-standard e-learning content packages. Without SCORM support, enterprise customers cannot migrate their existing training libraries into FlowFlex. Enterprise LMS dealbreaker.

**Panel:** `lms`  
**Phase:** 7 — required before any enterprise LMS sale

---

## Why Critical

Most organisations have existing training content built in Articulate Storyline, Adobe Captivate, iSpring, or bought from third-party content libraries. This content is packaged as SCORM or xAPI. Without a compatible player, they cannot use FlowFlex LMS.

---

## Standards Supported

| Standard | Version | Notes |
|---|---|---|
| SCORM 1.2 | 1.2 | Legacy but still most common |
| SCORM 2004 | 3rd/4th edition | Improved sequencing |
| xAPI (Tin Can) | 1.0 | Modern, supports mobile + offline |
| AICC | HACP | Older standard, some legacy content |
| cmi5 | 1.0 | xAPI profile for formal learning |

---

## Features

### Content Import
- Upload ZIP package (SCORM, xAPI, AICC)
- Validation on upload: check package structure, manifest file, launch file
- Thumbnail auto-extracted from package
- Course metadata extracted: title, description, estimated duration, objectives
- Version history: re-upload updated package without losing completion records

### SCORM Player
- Standards-compliant runtime environment (SCORM API shim)
- Rendered in sandboxed iFrame (security isolation)
- Communication: SCORM calls LMSSetValue/LMSGetValue → stored in completion tracking table
- Bookmark support: resume from last position
- Completion trigger: course marks completion via API (LMSSetValue cmi.core.lesson_status = "passed")
- Score passthrough: SCORM quiz score → LMS grade record

### xAPI / LRS
- Built-in Learning Record Store (LRS) for xAPI statements
- Store statements: actor, verb, object, result, context
- Query statements by learner, activity, verb, time range
- Export statements (JSON, CSV) for SCORM Cloud or third-party analytics

### Completion Tracking
- Track: not started / in progress / completed / passed / failed
- Score: raw score, scaled score, pass/fail threshold
- Time spent: total time in module
- Attempts: number of attempts
- Last accessed timestamp

### Authoring Tool Integration
- Publish directly from Articulate Storyline 360 → FlowFlex (via SCORM Cloud Reach API or direct upload)
- Articulate Rise 360 → FlowFlex
- Adobe Captivate → FlowFlex
- iSpring Suite → FlowFlex
- Lectora → FlowFlex

---

## Data Model

```erDiagram
    scorm_packages {
        ulid id PK
        ulid company_id FK
        ulid course_id FK
        string standard
        string version
        string launch_path
        string storage_path
        json manifest_data
        integer version_number
    }

    scorm_registrations {
        ulid id PK
        ulid package_id FK
        ulid learner_id FK
        string status
        decimal score_raw
        decimal score_scaled
        boolean passed
        integer time_spent_seconds
        integer attempts
        json cmi_data
        timestamp last_accessed_at
        timestamp completed_at
    }
```

---

## Permissions

```
lms.scorm.upload-packages
lms.scorm.view-completions
lms.scorm.export-records
```

---

## Competitors Displaced

SCORM Cloud · TalentLMS · Docebo · Cornerstone OnDemand · SAP SuccessFactors Learning

---

## Related

- [[MOC_LMS]]
- [[entity-employee]]
