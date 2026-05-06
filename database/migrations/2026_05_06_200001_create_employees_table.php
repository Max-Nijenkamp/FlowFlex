<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('company_id', 26)->index();
            $table->string('employee_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('national_id_encrypted')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->char('department_id', 26)->nullable()->index();
            $table->string('job_title')->nullable();
            $table->string('location')->nullable();
            $table->char('manager_id', 26)->nullable()->index();
            $table->date('start_date');
            $table->date('probation_end_date')->nullable();
            $table->tinyInteger('contracted_hours_per_week')->default(40);
            $table->string('employment_type')->default('full_time');
            $table->string('employment_status')->default('active');
            $table->char('profile_photo_file_id', 26)->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'employment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
