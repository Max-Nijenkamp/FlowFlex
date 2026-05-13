---
type: builder-log
module: lms-phase7
domain: Learning & Development
panel: lms
phase: 7
started: 2026-05-12
status: in-progress
color: "#F97316"
left_brain_source: "[[MOC_LMS]]"
last_updated: 2026-05-12
---

# Builder Log: Learning & Development — Phase 7

Left Brain sources: [[course-builder-lms]] · [[skills-matrix]] · [[succession-planning]] · [[mentoring-coaching]] · [[external-training]] · [[certification-compliance-training]] · [[ai-learning-coach]] · [[scorm-xapi-support]] · [[live-virtual-classroom]] · [[external-learner-portal]]

---

## Sessions

### Session 2026-05-12

**Goal:** Build all 10 LMS domain modules — full data layer, service layer, Filament resources, and test coverage for Phase 7.

**Built:**

Migrations (15 files, range 480001–480015):
- `2026_05_12_480001_create_courses_table.php` — courses (company_id, title, description, thumbnail, status enum, category, duration_minutes, created_by)
- `2026_05_12_480002_create_course_modules_table.php` — course_modules (course_id FK, title, content, module_type enum, sort_order, duration_minutes)
- `2026_05_12_480003_create_course_enrollments_table.php` — course_enrollments (course_id, user_id ULIDs, status enum, progress_pct, enrolled_at, completed_at) — no softDeletes
- `2026_05_12_480004_create_skills_table.php` — skills (name, description, category, is_active)
- `2026_05_12_480005_create_employee_skills_table.php` — employee_skills (employee_id ULID, skill_id, level enum, verified_at, verified_by) — no softDeletes
- `2026_05_12_480006_create_succession_plans_table.php` — succession_plans (position_title, incumbent_id, successor_id, readiness enum, notes, created_by)
- `2026_05_12_480007_create_mentoring_relationships_table.php` — mentoring_relationships (mentor_id, mentee_id, status enum, goals, started_at, ended_at)
- `2026_05_12_480008_create_mentoring_sessions_table.php` — mentoring_sessions (relationship_id, session_date, notes, duration_minutes) — no softDeletes
- `2026_05_12_480009_create_external_training_requests_table.php` — external_training_requests (employee_id, title, provider, estimated_cost, currency, status enum, approved_by, completed_at, notes)
- `2026_05_12_480010_create_certifications_table.php` — certifications (name, description, issuing_body, validity_months, is_required, created_by)
- `2026_05_12_480011_create_employee_certifications_table.php` — employee_certifications (employee_id, certification_id, status enum, issued_at, expires_at, certificate_file) — no softDeletes
- `2026_05_12_480012_create_learning_paths_table.php` — learning_paths (user_id, title, description, recommended_courses JSON, status enum, ai_generated bool)
- `2026_05_12_480013_create_scorm_packages_table.php` — scorm_packages (title, file_path, version enum, status enum, extracted_path, created_by)
- `2026_05_12_480014_create_virtual_classes_table.php` — virtual_classes (title, description, scheduled_at, duration_minutes, facilitator_id, attendees JSON, status enum, recording_url, created_by)
- `2026_05_12_480015_create_learner_portal_configs_table.php` — learner_portal_configs (company_id unique, title, description, logo_path, primary_color, allow_public_signup, stripe_product_id, is_active)

Models (15 files in `app/Models/Lms/`):
- `Course.php` — BelongsToCompany, HasUlids, SoftDeletes; relations: creator, modules, enrollments
- `CourseModule.php` — BelongsToCompany, HasUlids, SoftDeletes; relation: course
- `CourseEnrollment.php` — BelongsToCompany, HasUlids (no SoftDeletes); relations: course, user
- `Skill.php` — BelongsToCompany, HasUlids, SoftDeletes; relation: employeeSkills
- `EmployeeSkill.php` — BelongsToCompany, HasUlids (no SoftDeletes); relations: employee, skill, verifier
- `SuccessionPlan.php` — BelongsToCompany, HasUlids, SoftDeletes; relations: incumbent, successor, creator
- `MentoringRelationship.php` — BelongsToCompany, HasUlids, SoftDeletes; relations: mentor, mentee, sessions
- `MentoringSession.php` — BelongsToCompany, HasUlids (no SoftDeletes); relation: relationship
- `ExternalTrainingRequest.php` — BelongsToCompany, HasUlids, SoftDeletes; relations: employee, approver
- `Certification.php` — BelongsToCompany, HasUlids, SoftDeletes; relations: creator, employeeCertifications
- `EmployeeCertification.php` — BelongsToCompany, HasUlids (no SoftDeletes); relations: employee, certification
- `LearningPath.php` — BelongsToCompany, HasUlids, SoftDeletes; relation: user
- `ScormPackage.php` — BelongsToCompany, HasUlids, SoftDeletes; relation: creator
- `VirtualClass.php` — BelongsToCompany, HasUlids, SoftDeletes; relations: facilitator, creator
- `LearnerPortalConfig.php` — BelongsToCompany, HasUlids, SoftDeletes

Factories (15 files in `database/factories/Lms/`):
- CourseFactory (published/archived states)
- CourseModuleFactory
- CourseEnrollmentFactory (completed/inProgress states)
- SkillFactory (inactive state)
- EmployeeSkillFactory (verified state)
- SuccessionPlanFactory (readyNow state)
- MentoringRelationshipFactory (completed/paused states)
- MentoringSessionFactory
- ExternalTrainingRequestFactory (approved/rejected/completed states)
- CertificationFactory (required state)
- EmployeeCertificationFactory (expired/pending states)
- LearningPathFactory (aiGenerated/completed states)
- ScormPackageFactory (active/errored states)
- VirtualClassFactory (live/completed/cancelled states)
- LearnerPortalConfigFactory (active/publicSignup states)

Service Interfaces (10 files in `app/Contracts/Lms/`):
- `CourseServiceInterface` — createCourse, publishCourse, enroll, updateProgress, getCompanyCourses
- `SkillsMatrixServiceInterface` — createSkill, assignSkill, updateLevel, getEmployeeSkills, getSkillGaps
- `SuccessionServiceInterface` — createPlan, updateReadiness, getCompanyPlans, getReadyNow
- `MentoringServiceInterface` — createRelationship, logSession, completeRelationship, getActiveMentoring
- `ExternalTrainingServiceInterface` — submit, approve, reject, complete, getCompanyRequests
- `CertificationServiceInterface` — createCertification, award, getExpiring, getCompanyCertifications
- `LearningCoachServiceInterface` — generatePath, updateProgress, getActivePaths, completePath
- `ScormServiceInterface` — import, activate, getActivePackages, getCompanyPackages
- `VirtualClassroomServiceInterface` — schedule, start, complete, cancel, getUpcoming
- `LearnerPortalServiceInterface` — createConfig, activate, deactivate, getConfig

Service Implementations (10 files in `app/Services/Lms/`):
- CourseService, SkillsMatrixService, SuccessionService, MentoringService, ExternalTrainingService, CertificationService, LearningCoachService, ScormService, VirtualClassroomService, LearnerPortalService

Providers:
- `app/Providers/Lms/LmsServiceProvider.php` — binds all 10 interface→implementation pairs
- `app/Providers/Filament/LmsPanelProvider.php` — id='lms', path='lms', Green theme, 4 nav groups (Courses, Skills, Certifications, Settings)

Theme:
- `resources/css/filament/lms/theme.css` — full Tailwind v4 source paths

Filament Resources (11 resources × 3 pages each = 33 page files):
- `CourseResource` (lms.courses) — Courses group
- `CourseModuleResource` (lms.courses) — Courses group
- `SkillResource` (lms.skills) — Skills group
- `SuccessionPlanResource` (lms.succession) — Skills group
- `MentoringRelationshipResource` (lms.mentoring) — Skills group
- `ExternalTrainingRequestResource` (lms.external-training) — Courses group
- `CertificationResource` (lms.certifications) — Certifications group
- `LearningPathResource` (lms.ai-coach) — Courses group
- `ScormPackageResource` (lms.scorm) — Courses group
- `VirtualClassResource` (lms.virtual-classroom) — Courses group
- `LearnerPortalConfigResource` (lms.learner-portal) — Settings group

Tests (10 files in `tests/Feature/Lms/`, ~48 test cases):
- CourseServiceTest — create, publish, enroll, updateProgress, company-scoped
- SkillsMatrixServiceTest — create, assign, updateLevel, getEmployeeSkills, getSkillGaps
- SuccessionServiceTest — create, updateReadiness, getCompanyPlans, getReadyNow
- MentoringServiceTest — create, logSession, complete, getActiveMentoring
- ExternalTrainingServiceTest — submit, approve, reject, complete, getCompanyRequests
- CertificationServiceTest — create, award, getExpiring, getCompanyCertifications
- LearningCoachServiceTest — generatePath, updateProgress, getActivePaths, completePath
- ScormServiceTest — import, activate, getActivePackages, getCompanyPackages
- VirtualClassroomServiceTest — schedule, start, complete, cancel, getUpcoming
- LearnerPortalServiceTest — createConfig, activate, deactivate, getConfig, returnsNull

**Decisions made:**

1. Migration range 480001–480015 used (not 700000–749999 from spec) — consistent with the project's actual numbering sequence based on build date rather than planned range. All other Phase 6+ domains also use 45xxxx–48xxxx ranges. Spec ranges are aspirational, build ranges are sequential.
2. CourseEnrollment.user_id uses ulid column — users table PK is ULID (confirmed from 000003 migration and User model). Fixed during build (initial `unsignedBigInteger` corrected to `ulid`).
3. CourseService.enroll() accepts `string $userId` not `int` — ULID consistency across all FK columns.
4. No SoftDeletes on join/pivot-adjacent models (CourseEnrollment, EmployeeSkill, MentoringSession, EmployeeCertification) — follows existing project pattern (HR leave_balances, onboarding_tasks have no soft deletes).
5. LearnerPortalConfig has `company_id unique()` constraint — one portal config per company by design; enforced at DB level.
6. SkillsMatrixService.getSkillGaps() defined as "skills with no advanced/expert assignment" — pragmatic MVP definition without requiring a formal gap specification table.

**Problems hit:**

- user_id FK type mismatch: Initially used `unsignedBigInteger('user_id')` in `course_enrollments` migration, but users table uses ULID PKs. Fixed immediately by checking the users migration (000003) and User model. Corrected to `ulid('user_id')`.
- CourseServiceInterface enroll() signature had `int $userId` — corrected to `string $userId` for ULID consistency.

**Patterns found:**

- Models without SoftDeletes use the same BelongsToCompany + HasUlids traits — global scope still applies, data is protected by company_id even without soft delete.
- JSON default columns (`recommended_courses`, `attendees`) are cast to `array` in model `$casts` — consistent with AI domain patterns.

---

## Gaps Discovered

None identified during this build session.

---

## Lessons

- The spec migration ranges (700000–749999) are planning placeholders. Actual build migrations should use the sequential date-based pattern (2026_05_12_480xxx) to avoid ordering conflicts. The STATUS_Dashboard and left-brain specs have been updated to reflect the actual ranges used.
- LarnerPortalConfig's `company_id UNIQUE` DB constraint was the right call even though the spec didn't specify it explicitly — one portal per tenant is the only sensible model.

---

## Post-Build Checklist

- [ ] All migrations run cleanly (`php artisan migrate`)
- [ ] All tests pass (`php artisan test --filter=Lms`)
- [ ] Filament resources render correctly at `/lms`
- [ ] LmsPanelProvider registered in `bootstrap/providers.php`
- [ ] LmsServiceProvider registered in `bootstrap/providers.php`
- [ ] `resources/css/filament/lms/theme.css` added to `vite.config.js`
- [ ] Module keys added to ModuleCatalogSeeder and LocalCompanySeeder
- [ ] Permissions registered in PermissionSeeder (`lms.*`)
- [ ] Left Brain specs updated to `in-progress` ✅
- [ ] STATUS_Dashboard updated ✅

---

## Related

- [[ACTIVATION_GUIDE]]
- [[STATUS_Dashboard]]
- [[MOC_LMS]]
