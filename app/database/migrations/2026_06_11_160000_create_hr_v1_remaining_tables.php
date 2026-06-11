<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- hr.recruitment ---
        Schema::create('hr_job_requisitions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('employment_type'); // full-time/part-time/contractor
            $table->string('status')->default('draft'); // draft / open / closed
            $table->string('slug');
            $table->date('open_date')->nullable();
            $table->integer('headcount')->default(1);
            $table->foreignUlid('department_id')->nullable()->constrained('hr_departments')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'slug']);
        });

        Schema::create('hr_applicants', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('requisition_id')->constrained('hr_job_requisitions')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable(); // E.164
            $table->string('cv_path')->nullable(); // tenant-scoped via core.files
            $table->string('status')->default('applied'); // state machine
            $table->string('source')->nullable(); // careers / referral / manual
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'requisition_id', 'status']);
        });

        Schema::create('hr_interviews', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('applicant_id')->constrained('hr_applicants')->cascadeOnDelete();
            $table->timestamp('scheduled_at');
            $table->json('interviewers'); // user ids
            $table->string('type'); // video / phone / on-site
            $table->string('outcome')->nullable(); // pass / fail / pending
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_offers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('applicant_id')->constrained('hr_applicants')->cascadeOnDelete();
            $table->text('salary_raw')->nullable(); // encrypted minor-unit integer (mirrors payroll)
            $table->string('currency', 3)->default('EUR');
            $table->date('start_date');
            $table->string('status')->default('draft'); // draft / sent / accepted / declined
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
        });

        // --- hr.performance ---
        Schema::create('hr_review_cycles', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->date('period_start');
            $table->date('period_end');
            $table->string('type')->default('annual'); // annual / bi-annual / quarterly
            $table->json('rating_scale')->nullable(); // default 1-5
            $table->string('status')->default('draft'); // state machine
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
        });

        Schema::create('hr_reviews', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('cycle_id')->constrained('hr_review_cycles')->cascadeOnDelete();
            $table->foreignUlid('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->ulid('reviewer_id')->nullable(); // hr_employees; null self
            $table->string('type'); // self / manager / peer
            $table->string('status')->default('pending'); // pending / submitted
            $table->decimal('rating', 3, 1)->nullable();
            $table->json('content')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['cycle_id', 'employee_id', 'reviewer_id', 'type']);
        });

        Schema::create('hr_review_goals', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('review_id')->constrained('hr_reviews')->cascadeOnDelete();
            $table->foreignUlid('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('progress_percent')->default(0);
            $table->decimal('rating', 3, 1)->nullable();
            $table->timestamps();
        });

        // --- hr.time ---
        Schema::create('hr_timesheets', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->date('week_start');
            $table->integer('total_minutes')->default(0);
            $table->string('status')->default('draft'); // state machine
            $table->foreignUlid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'employee_id', 'week_start']);
        });

        Schema::create('hr_time_entries', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->date('date');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->integer('break_minutes')->default(0);
            $table->integer('total_minutes')->default(0);
            $table->boolean('is_overtime')->default(false);
            $table->text('notes')->nullable();
            $table->foreignUlid('timesheet_id')->nullable()->constrained('hr_timesheets')->nullOnDelete();
            $table->timestamps();

            $table->unique(['company_id', 'employee_id', 'date']);
        });

        // --- hr.shifts ---
        Schema::create('hr_shifts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->ulid('employee_id')->nullable(); // null = coverage gap
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('role');
            $table->string('status')->default('draft'); // draft / published / cancelled
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'date', 'status']);
            $table->index(['company_id', 'employee_id', 'date']);
        });

        Schema::create('hr_shift_swap_requests', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('requester_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignUlid('recipient_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignUlid('shift_id')->constrained('hr_shifts')->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending / accepted / approved / declined
            $table->timestamp('manager_approved_at')->nullable();
            $table->timestamps();
        });

        // --- hr.compensation ---
        Schema::create('hr_compensation_bands', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('job_grade');
            $table->foreignUlid('department_id')->nullable()->constrained('hr_departments')->nullOnDelete();
            $table->bigInteger('min_salary_cents');
            $table->bigInteger('mid_salary_cents');
            $table->bigInteger('max_salary_cents');
            $table->string('currency', 3)->default('EUR');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'job_grade', 'department_id']);
        });

        Schema::create('hr_benefits', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('type'); // insurance / pension / allowance
            $table->bigInteger('cost_per_month_cents')->default(0);
            $table->bigInteger('employer_contribution_cents')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hr_employee_benefits', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignUlid('benefit_id')->constrained('hr_benefits')->cascadeOnDelete();
            $table->timestamp('enrolled_at');
            $table->timestamp('unenrolled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_salary_history', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->text('amount_raw'); // encrypted minor-unit integer
            $table->string('salary_band')->nullable(); // coarse, derived
            $table->date('effective_date');
            $table->string('reason'); // hire / promotion / comp-review / correction
            $table->foreignUlid('changed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps(); // append-only — never updated/deleted
        });

        // --- hr.workforce ---
        Schema::create('hr_headcount_plans', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('department_id')->nullable()->constrained('hr_departments')->nullOnDelete();
            $table->string('period'); // 2026-Q3 / 2027
            $table->integer('target_headcount');
            $table->integer('expected_attrition')->default(0);
            $table->bigInteger('budgeted_cost_cents')->default(0);
            $table->string('currency', 3)->default('EUR');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'department_id', 'period']);
        });

        Schema::create('hr_planned_roles', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('plan_id')->constrained('hr_headcount_plans')->cascadeOnDelete();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('title');
            $table->date('target_start_date');
            $table->bigInteger('budgeted_salary_cents')->default(0);
            $table->string('status')->default('planned'); // planned / approved / filled
            $table->ulid('requisition_id')->nullable();
            $table->timestamps();
        });

        // --- hr.feedback ---
        Schema::create('hr_feedback', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('from_employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignUlid('to_employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->string('type'); // praise / constructive / coaching-note
            $table->text('message');
            $table->string('visibility'); // public / private / manager-chain
            $table->ulid('related_goal_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'to_employee_id']);
            $table->index(['company_id', 'visibility', 'created_at']);
        });

        Schema::create('hr_one_on_ones', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('manager_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignUlid('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->date('meeting_date');
            $table->text('agenda')->nullable();
            $table->text('notes')->nullable(); // participants only
            $table->json('action_items')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // --- hr.dei ---
        Schema::create('hr_dei_attributes', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->string('dimension'); // gender / age-band / ethnicity / disability
            $table->text('value'); // encrypted — never indexed/filtered
            $table->timestamp('consented_at');
            $table->timestamps(); // hard-deleted on erasure or consent withdrawal

            $table->unique(['employee_id', 'dimension']);
        });

        Schema::create('hr_dei_snapshots', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('period'); // 2026-Q2
            $table->string('dimension');
            $table->json('breakdown'); // aggregated only, small groups suppressed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['hr_dei_snapshots', 'hr_dei_attributes', 'hr_one_on_ones', 'hr_feedback',
            'hr_planned_roles', 'hr_headcount_plans', 'hr_salary_history', 'hr_employee_benefits',
            'hr_benefits', 'hr_compensation_bands', 'hr_shift_swap_requests', 'hr_shifts',
            'hr_time_entries', 'hr_timesheets', 'hr_review_goals', 'hr_reviews', 'hr_review_cycles',
            'hr_offers', 'hr_interviews', 'hr_applicants', 'hr_job_requisitions'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
