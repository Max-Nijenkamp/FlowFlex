<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Departments first — employees reference them; the head FK is added
        // after hr_employees exists (self-referential pair, pgsql ordering).
        Schema::create('hr_departments', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->ulid('parent_department_id')->nullable();
            $table->ulid('head_employee_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'name']);
        });

        Schema::create('hr_employees', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('employee_number');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email'); // work email
            $table->string('phone')->nullable(); // E.164
            $table->text('personal_email')->nullable(); // encrypted
            $table->text('date_of_birth')->nullable(); // encrypted
            $table->smallInteger('birth_year')->nullable();
            $table->text('national_id')->nullable(); // encrypted
            $table->text('national_id_hash')->nullable()->index();
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->text('termination_reason')->nullable();
            $table->string('job_title');
            $table->foreignUlid('department_id')->nullable()->constrained('hr_departments')->nullOnDelete();
            $table->ulid('manager_id')->nullable(); // self FK below
            $table->string('employment_type'); // full-time / part-time / contractor
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'employee_number']);
            $table->unique(['company_id', 'email']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'department_id']);
            $table->index(['company_id', 'manager_id']);
        });

        Schema::table('hr_employees', function (Blueprint $table): void {
            $table->foreign('manager_id')->references('id')->on('hr_employees')->nullOnDelete();
        });

        Schema::table('hr_departments', function (Blueprint $table): void {
            $table->foreign('head_employee_id')->references('id')->on('hr_employees')->nullOnDelete();
            $table->foreign('parent_department_id')->references('id')->on('hr_departments')->nullOnDelete();
        });

        Schema::create('hr_emergency_contacts', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->string('name');
            $table->string('relationship');
            $table->string('phone'); // E.164
            $table->string('email')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_emergency_contacts');
        Schema::table('hr_departments', function (Blueprint $table): void {
            $table->dropForeign(['head_employee_id']);
        });
        Schema::dropIfExists('hr_employees');
        Schema::dropIfExists('hr_departments');
    }
};
