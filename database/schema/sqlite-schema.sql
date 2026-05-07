CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "password" varchar not null,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "two_factor_secret" text,
  "two_factor_recovery_codes" text,
  "two_factor_confirmed_at" datetime
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE INDEX "cache_expiration_index" on "cache"("expiration");
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE INDEX "cache_locks_expiration_index" on "cache_locks"("expiration");
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "tenants"(
  "id" varchar not null,
  "company_id" varchar not null,
  "first_name" varchar not null,
  "middle_name" varchar,
  "last_name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "phone" varchar,
  "password" varchar not null,
  "is_enabled" tinyint(1) not null default '1',
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("company_id") references "companies"("id") on delete cascade,
  primary key("id")
);
CREATE UNIQUE INDEX "tenants_email_unique" on "tenants"("email");
CREATE TABLE IF NOT EXISTS "addresses"(
  "id" varchar not null,
  "addressable_type" varchar not null,
  "addressable_id" varchar not null,
  "country" varchar not null default 'NL',
  "city" varchar not null,
  "street" varchar not null,
  "postal_code" varchar not null,
  "house_number" varchar not null,
  "house_number_addition" varchar,
  "is_primary" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  primary key("id")
);
CREATE INDEX "addresses_addressable_type_addressable_id_index" on "addresses"(
  "addressable_type",
  "addressable_id"
);
CREATE TABLE IF NOT EXISTS "permissions"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "guard_name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "permissions_name_guard_name_unique" on "permissions"(
  "name",
  "guard_name"
);
CREATE TABLE IF NOT EXISTS "roles"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "guard_name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "roles_name_guard_name_unique" on "roles"(
  "name",
  "guard_name"
);
CREATE TABLE IF NOT EXISTS "model_has_permissions"(
  "permission_id" integer not null,
  "model_type" varchar not null,
  "model_id" integer not null,
  foreign key("permission_id") references "permissions"("id") on delete cascade,
  primary key("permission_id", "model_id", "model_type")
);
CREATE INDEX "model_has_permissions_model_id_model_type_index" on "model_has_permissions"(
  "model_id",
  "model_type"
);
CREATE TABLE IF NOT EXISTS "model_has_roles"(
  "role_id" integer not null,
  "model_type" varchar not null,
  "model_id" integer not null,
  foreign key("role_id") references "roles"("id") on delete cascade,
  primary key("role_id", "model_id", "model_type")
);
CREATE INDEX "model_has_roles_model_id_model_type_index" on "model_has_roles"(
  "model_id",
  "model_type"
);
CREATE TABLE IF NOT EXISTS "role_has_permissions"(
  "permission_id" integer not null,
  "role_id" integer not null,
  foreign key("permission_id") references "permissions"("id") on delete cascade,
  foreign key("role_id") references "roles"("id") on delete cascade,
  primary key("permission_id", "role_id")
);
CREATE TABLE IF NOT EXISTS "modules"(
  "id" varchar not null,
  "key" varchar not null,
  "name" varchar not null,
  "description" text,
  "domain" varchar,
  "panel_id" varchar,
  "icon" varchar,
  "color" varchar,
  "sort_order" integer,
  "is_core" tinyint(1) not null default '0',
  "is_available" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  primary key("id")
);
CREATE UNIQUE INDEX "modules_key_unique" on "modules"("key");
CREATE TABLE IF NOT EXISTS "sub_modules"(
  "id" varchar not null,
  "module_id" varchar not null,
  "key" varchar not null,
  "name" varchar not null,
  "description" text,
  "sort_order" integer,
  "is_available" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("module_id") references "modules"("id") on delete cascade,
  primary key("id")
);
CREATE UNIQUE INDEX "sub_modules_key_unique" on "sub_modules"("key");
CREATE TABLE IF NOT EXISTS "company_module"(
  "id" varchar not null,
  "company_id" varchar not null,
  "module_id" varchar not null,
  "is_enabled" tinyint(1) not null default '0',
  "enabled_at" datetime,
  "disabled_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("company_id") references "companies"("id") on delete cascade,
  foreign key("module_id") references "modules"("id") on delete cascade,
  primary key("id")
);
CREATE UNIQUE INDEX "company_module_company_id_module_id_unique" on "company_module"(
  "company_id",
  "module_id"
);
CREATE TABLE IF NOT EXISTS "activity_log"(
  "id" integer primary key autoincrement not null,
  "log_name" varchar,
  "description" text not null,
  "subject_type" varchar,
  "subject_id" integer,
  "event" varchar,
  "causer_type" varchar,
  "causer_id" integer,
  "attribute_changes" text,
  "properties" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "subject" on "activity_log"("subject_type", "subject_id");
CREATE INDEX "causer" on "activity_log"("causer_type", "causer_id");
CREATE INDEX "activity_log_log_name_index" on "activity_log"("log_name");
CREATE TABLE IF NOT EXISTS "companies"(
  "id" varchar not null,
  "name" varchar not null,
  "slug" varchar not null,
  "email" varchar not null,
  "phone" varchar,
  "website" varchar,
  "timezone" varchar not null default('UTC'),
  "locale" varchar not null default 'en',
  "settings" text,
  "is_enabled" tinyint(1) not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "currency" varchar not null default 'EUR',
  "logo_file_id" varchar,
  primary key("id")
);
CREATE UNIQUE INDEX "companies_slug_unique" on "companies"("slug");
CREATE TABLE IF NOT EXISTS "notifications"(
  "id" varchar not null,
  "type" varchar not null,
  "notifiable_type" varchar not null,
  "notifiable_id" varchar not null,
  "data" text not null,
  "read_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  primary key("id")
);
CREATE INDEX "notifications_notifiable_type_notifiable_id_index" on "notifications"(
  "notifiable_type",
  "notifiable_id"
);
CREATE TABLE IF NOT EXISTS "notification_preferences"(
  "id" varchar not null,
  "company_id" varchar not null,
  "tenant_id" varchar not null,
  "notification_type" varchar not null,
  "channels" text not null,
  "is_enabled" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE UNIQUE INDEX "notification_preferences_tenant_id_notification_type_unique" on "notification_preferences"(
  "tenant_id",
  "notification_type"
);
CREATE INDEX "notification_preferences_company_id_index" on "notification_preferences"(
  "company_id"
);
CREATE INDEX "notification_preferences_tenant_id_index" on "notification_preferences"(
  "tenant_id"
);
CREATE TABLE IF NOT EXISTS "api_keys"(
  "id" varchar not null,
  "company_id" varchar not null,
  "created_by_tenant_id" varchar,
  "name" varchar not null,
  "key_hash" varchar not null,
  "key_prefix" varchar not null,
  "scopes" text,
  "last_used_at" datetime,
  "expires_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "api_keys_company_id_index" on "api_keys"("company_id");
CREATE UNIQUE INDEX "api_keys_key_hash_unique" on "api_keys"("key_hash");
CREATE TABLE IF NOT EXISTS "files"(
  "id" varchar not null,
  "company_id" varchar not null,
  "uploaded_by_tenant_id" varchar,
  "disk" varchar not null default 's3',
  "path" varchar not null,
  "original_name" varchar not null,
  "mime_type" varchar,
  "size" integer not null,
  "collection" varchar,
  "model_type" varchar,
  "model_id" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "files_model_type_model_id_index" on "files"(
  "model_type",
  "model_id"
);
CREATE INDEX "files_company_id_collection_index" on "files"(
  "company_id",
  "collection"
);
CREATE INDEX "files_company_id_index" on "files"("company_id");
CREATE TABLE IF NOT EXISTS "departments"(
  "id" varchar not null,
  "company_id" varchar not null,
  "name" varchar not null,
  "description" text,
  "manager_id" varchar,
  "parent_department_id" varchar,
  "color" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "departments_company_id_index" on "departments"("company_id");
CREATE INDEX "departments_manager_id_index" on "departments"("manager_id");
CREATE INDEX "departments_parent_department_id_index" on "departments"(
  "parent_department_id"
);
CREATE TABLE IF NOT EXISTS "employee_documents"(
  "id" varchar not null,
  "company_id" varchar not null,
  "employee_id" varchar not null,
  "file_id" varchar not null,
  "category" varchar not null,
  "title" varchar not null,
  "notes" text,
  "version" integer not null default '1',
  "is_latest" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "employee_documents_company_id_index" on "employee_documents"(
  "company_id"
);
CREATE INDEX "employee_documents_employee_id_index" on "employee_documents"(
  "employee_id"
);
CREATE INDEX "employee_documents_file_id_index" on "employee_documents"(
  "file_id"
);
CREATE TABLE IF NOT EXISTS "employee_custom_fields"(
  "id" varchar not null,
  "company_id" varchar not null,
  "name" varchar not null,
  "key" varchar not null,
  "field_type" varchar not null default 'text',
  "options" text,
  "is_required" tinyint(1) not null default '0',
  "is_visible_to_employee" tinyint(1) not null default '1',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE UNIQUE INDEX "employee_custom_fields_company_id_key_unique" on "employee_custom_fields"(
  "company_id",
  "key"
);
CREATE INDEX "employee_custom_fields_company_id_index" on "employee_custom_fields"(
  "company_id"
);
CREATE TABLE IF NOT EXISTS "employee_custom_field_values"(
  "id" varchar not null,
  "company_id" varchar not null,
  "employee_id" varchar not null,
  "custom_field_id" varchar not null,
  "value" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE UNIQUE INDEX "employee_custom_field_values_employee_id_custom_field_id_unique" on "employee_custom_field_values"(
  "employee_id",
  "custom_field_id"
);
CREATE INDEX "employee_custom_field_values_company_id_index" on "employee_custom_field_values"(
  "company_id"
);
CREATE INDEX "employee_custom_field_values_employee_id_index" on "employee_custom_field_values"(
  "employee_id"
);
CREATE INDEX "employee_custom_field_values_custom_field_id_index" on "employee_custom_field_values"(
  "custom_field_id"
);
CREATE TABLE IF NOT EXISTS "onboarding_templates"(
  "id" varchar not null,
  "company_id" varchar not null,
  "name" varchar not null,
  "description" text,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "onboarding_templates_company_id_index" on "onboarding_templates"(
  "company_id"
);
CREATE TABLE IF NOT EXISTS "onboarding_template_tasks"(
  "id" varchar not null,
  "company_id" varchar not null,
  "template_id" varchar not null,
  "title" varchar not null,
  "description" text,
  "task_type" varchar not null default 'read_acknowledge',
  "default_assignee" varchar,
  "due_day_offset" integer not null default '1',
  "is_required" tinyint(1) not null default '1',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "onboarding_template_tasks_company_id_index" on "onboarding_template_tasks"(
  "company_id"
);
CREATE INDEX "onboarding_template_tasks_template_id_index" on "onboarding_template_tasks"(
  "template_id"
);
CREATE TABLE IF NOT EXISTS "onboarding_checkins"(
  "id" varchar not null,
  "company_id" varchar not null,
  "employee_id" varchar not null,
  "day_milestone" integer not null,
  "scheduled_at" datetime not null,
  "completed_at" datetime,
  "status" varchar not null default 'pending',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "onboarding_checkins_company_id_index" on "onboarding_checkins"(
  "company_id"
);
CREATE INDEX "onboarding_checkins_employee_id_index" on "onboarding_checkins"(
  "employee_id"
);
CREATE TABLE IF NOT EXISTS "onboarding_checkin_responses"(
  "id" varchar not null,
  "company_id" varchar not null,
  "checkin_id" varchar not null,
  "respondent_tenant_id" varchar not null,
  "responses" text not null,
  "score" integer,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "onboarding_checkin_responses_company_id_index" on "onboarding_checkin_responses"(
  "company_id"
);
CREATE INDEX "onboarding_checkin_responses_checkin_id_index" on "onboarding_checkin_responses"(
  "checkin_id"
);
CREATE INDEX "onboarding_checkin_responses_respondent_tenant_id_index" on "onboarding_checkin_responses"(
  "respondent_tenant_id"
);
CREATE TABLE IF NOT EXISTS "leave_types"(
  "id" varchar not null,
  "company_id" varchar not null,
  "name" varchar not null,
  "code" varchar,
  "description" text,
  "color" varchar,
  "is_paid" tinyint(1) not null default '1',
  "requires_approval" tinyint(1) not null default '1',
  "min_notice_days" integer not null default '0',
  "allow_half_day" tinyint(1) not null default '1',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE UNIQUE INDEX "leave_types_company_id_code_unique" on "leave_types"(
  "company_id",
  "code"
);
CREATE INDEX "leave_types_company_id_index" on "leave_types"("company_id");
CREATE TABLE IF NOT EXISTS "leave_policies"(
  "id" varchar not null,
  "company_id" varchar not null,
  "leave_type_id" varchar not null,
  "accrual_type" varchar not null default 'immediate',
  "annual_entitlement_days" numeric not null default '0',
  "max_carry_over_days" numeric not null default '0',
  "allow_negative" tinyint(1) not null default '0',
  "probation_restriction_months" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "leave_policies_company_id_index" on "leave_policies"(
  "company_id"
);
CREATE INDEX "leave_policies_leave_type_id_index" on "leave_policies"(
  "leave_type_id"
);
CREATE TABLE IF NOT EXISTS "public_holidays"(
  "id" varchar not null,
  "company_id" varchar,
  "country_code" varchar not null,
  "name" varchar not null,
  "date" date not null,
  "is_recurring" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "public_holidays_company_id_index" on "public_holidays"(
  "company_id"
);
CREATE TABLE IF NOT EXISTS "payroll_entities"(
  "id" varchar not null,
  "company_id" varchar not null,
  "name" varchar not null,
  "legal_name" varchar,
  "country_code" varchar not null,
  "tax_reference" varchar,
  "is_default" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "payroll_entities_company_id_index" on "payroll_entities"(
  "company_id"
);
CREATE TABLE IF NOT EXISTS "pay_elements"(
  "id" varchar not null,
  "company_id" varchar not null,
  "payroll_entity_id" varchar,
  "name" varchar not null,
  "element_type" varchar not null default 'basic_salary',
  "is_taxable" tinyint(1) not null default '1',
  "is_pensionable" tinyint(1) not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "pay_elements_company_id_index" on "pay_elements"("company_id");
CREATE INDEX "pay_elements_payroll_entity_id_index" on "pay_elements"(
  "payroll_entity_id"
);
CREATE TABLE IF NOT EXISTS "tax_configurations"(
  "id" varchar not null,
  "company_id" varchar not null,
  "payroll_entity_id" varchar not null,
  "country_code" varchar not null,
  "tax_year" integer not null,
  "configuration" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE UNIQUE INDEX "tax_configurations_payroll_entity_id_tax_year_unique" on "tax_configurations"(
  "payroll_entity_id",
  "tax_year"
);
CREATE INDEX "tax_configurations_company_id_index" on "tax_configurations"(
  "company_id"
);
CREATE INDEX "tax_configurations_payroll_entity_id_index" on "tax_configurations"(
  "payroll_entity_id"
);
CREATE TABLE IF NOT EXISTS "deductions"(
  "id" varchar not null,
  "company_id" varchar not null,
  "employee_id" varchar not null,
  "pay_element_id" varchar,
  "name" varchar not null,
  "deduction_type" varchar not null default 'other',
  "amount" numeric not null,
  "is_percentage" tinyint(1) not null default '0',
  "is_recurring" tinyint(1) not null default '1',
  "effective_from" date not null,
  "effective_to" date,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "deductions_company_id_index" on "deductions"("company_id");
CREATE INDEX "deductions_employee_id_index" on "deductions"("employee_id");
CREATE INDEX "deductions_pay_element_id_index" on "deductions"(
  "pay_element_id"
);
CREATE TABLE IF NOT EXISTS "contractor_payments"(
  "id" varchar not null,
  "company_id" varchar not null,
  "pay_run_id" varchar,
  "employee_id" varchar not null,
  "amount" numeric not null,
  "currency" varchar not null default 'EUR',
  "reference" varchar,
  "status" varchar not null default 'pending',
  "processed_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "contractor_payments_company_id_index" on "contractor_payments"(
  "company_id"
);
CREATE INDEX "contractor_payments_pay_run_id_index" on "contractor_payments"(
  "pay_run_id"
);
CREATE INDEX "contractor_payments_employee_id_index" on "contractor_payments"(
  "employee_id"
);
CREATE TABLE IF NOT EXISTS "task_labels"(
  "id" varchar not null,
  "company_id" varchar not null,
  "name" varchar not null,
  "color" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "task_labels_company_id_index" on "task_labels"("company_id");
CREATE TABLE IF NOT EXISTS "task_dependencies"(
  "id" varchar not null,
  "company_id" varchar not null,
  "task_id" varchar not null,
  "depends_on_task_id" varchar not null,
  "dependency_type" varchar not null default 'blocks',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "task_dependencies_company_id_index" on "task_dependencies"(
  "company_id"
);
CREATE INDEX "task_dependencies_task_id_index" on "task_dependencies"(
  "task_id"
);
CREATE INDEX "task_dependencies_depends_on_task_id_index" on "task_dependencies"(
  "depends_on_task_id"
);
CREATE TABLE IF NOT EXISTS "task_automations"(
  "id" varchar not null,
  "company_id" varchar not null,
  "name" varchar not null,
  "trigger_type" varchar not null,
  "trigger_conditions" text,
  "action_type" varchar not null,
  "action_config" text not null,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "task_automations_company_id_index" on "task_automations"(
  "company_id"
);
CREATE TABLE IF NOT EXISTS "task_automation_logs"(
  "id" varchar not null,
  "company_id" varchar not null,
  "automation_id" varchar not null,
  "task_id" varchar not null,
  "triggered_at" datetime not null,
  "success" tinyint(1) not null default '1',
  "error_message" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "task_automation_logs_company_id_index" on "task_automation_logs"(
  "company_id"
);
CREATE INDEX "task_automation_logs_automation_id_index" on "task_automation_logs"(
  "automation_id"
);
CREATE INDEX "task_automation_logs_task_id_index" on "task_automation_logs"(
  "task_id"
);
CREATE TABLE IF NOT EXISTS "timesheet_approvals"(
  "id" varchar not null,
  "company_id" varchar not null,
  "timesheet_id" varchar not null,
  "approver_tenant_id" varchar not null,
  "status" varchar not null default 'pending',
  "notes" text,
  "decided_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "timesheet_approvals_company_id_index" on "timesheet_approvals"(
  "company_id"
);
CREATE INDEX "timesheet_approvals_timesheet_id_index" on "timesheet_approvals"(
  "timesheet_id"
);
CREATE INDEX "timesheet_approvals_approver_tenant_id_index" on "timesheet_approvals"(
  "approver_tenant_id"
);
CREATE TABLE IF NOT EXISTS "document_versions"(
  "id" varchar not null,
  "company_id" varchar not null,
  "document_id" varchar not null,
  "file_id" varchar not null,
  "version_number" integer not null,
  "uploaded_by_tenant_id" varchar,
  "change_notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "document_versions_company_id_index" on "document_versions"(
  "company_id"
);
CREATE INDEX "document_versions_document_id_index" on "document_versions"(
  "document_id"
);
CREATE INDEX "document_versions_file_id_index" on "document_versions"(
  "file_id"
);
CREATE INDEX "document_versions_uploaded_by_tenant_id_index" on "document_versions"(
  "uploaded_by_tenant_id"
);
CREATE TABLE IF NOT EXISTS "document_shares"(
  "id" varchar not null,
  "company_id" varchar not null,
  "document_id" varchar not null,
  "share_token" varchar not null,
  "expires_at" datetime,
  "password_protected" tinyint(1) not null default '0',
  "password_hash" varchar,
  "download_only" tinyint(1) not null default '1',
  "view_count" integer not null default '0',
  "created_by_tenant_id" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE INDEX "document_shares_company_id_index" on "document_shares"(
  "company_id"
);
CREATE INDEX "document_shares_document_id_index" on "document_shares"(
  "document_id"
);
CREATE UNIQUE INDEX "document_shares_share_token_unique" on "document_shares"(
  "share_token"
);
CREATE UNIQUE INDEX "task_dependencies_task_id_depends_on_task_id_unique" on "task_dependencies"(
  "task_id",
  "depends_on_task_id"
);
CREATE TABLE IF NOT EXISTS "task_label_assignments"(
  "task_id" varchar NOT NULL,
  "label_id" varchar NOT NULL,
  "created_at" datetime,
  "updated_at" datetime,
  PRIMARY KEY("task_id", "label_id")
);
CREATE TABLE IF NOT EXISTS "employees"(
  "id" varchar not null,
  "company_id" varchar not null,
  "employee_number" varchar,
  "first_name" varchar not null,
  "last_name" varchar not null,
  "middle_name" varchar,
  "email" varchar not null,
  "phone" varchar,
  "date_of_birth" date,
  "national_id_encrypted" text,
  "emergency_contact_name" varchar,
  "emergency_contact_phone" varchar,
  "emergency_contact_relationship" varchar,
  "department_id" varchar,
  "job_title" varchar,
  "location" varchar,
  "manager_id" varchar,
  "start_date" date not null,
  "probation_end_date" date,
  "contracted_hours_per_week" integer not null default('40'),
  "employment_type" varchar not null default('full_time'),
  "employment_status" varchar not null default('active'),
  "profile_photo_file_id" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("department_id") references departments("id") on delete set null on update no action,
  foreign key("company_id") references companies("id") on delete cascade on update no action,
  foreign key("manager_id") references "employees"("id") on delete set null,
  primary key("id")
);
CREATE UNIQUE INDEX "employees_company_id_employee_number_unique" on "employees"(
  "company_id",
  "employee_number"
);
CREATE INDEX "employees_company_id_employment_status_index" on "employees"(
  "company_id",
  "employment_status"
);
CREATE INDEX "employees_company_id_index" on "employees"("company_id");
CREATE INDEX "employees_department_id_index" on "employees"("department_id");
CREATE INDEX "employees_manager_id_index" on "employees"("manager_id");
CREATE INDEX "employees_profile_photo_file_id_index" on "employees"(
  "profile_photo_file_id"
);
CREATE TABLE IF NOT EXISTS "leave_requests"(
  "id" varchar not null,
  "company_id" varchar not null,
  "employee_id" varchar not null,
  "leave_type_id" varchar not null,
  "start_date" date not null,
  "end_date" date not null,
  "total_days" numeric not null,
  "is_half_day" tinyint(1) not null default('0'),
  "reason" text,
  "status" varchar not null default('pending'),
  "approved_by_tenant_id" varchar,
  "approved_at" datetime,
  "rejection_reason" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("employee_id") references employees("id") on delete cascade on update no action,
  foreign key("leave_type_id") references "leave_types"("id") on delete restrict,
  primary key("id")
);
CREATE INDEX "leave_requests_approved_by_tenant_id_index" on "leave_requests"(
  "approved_by_tenant_id"
);
CREATE INDEX "leave_requests_company_id_index" on "leave_requests"(
  "company_id"
);
CREATE INDEX "leave_requests_employee_id_index" on "leave_requests"(
  "employee_id"
);
CREATE INDEX "leave_requests_leave_type_id_index" on "leave_requests"(
  "leave_type_id"
);
CREATE TABLE IF NOT EXISTS "leave_balances"(
  "id" varchar not null,
  "company_id" varchar not null,
  "employee_id" varchar not null,
  "leave_type_id" varchar not null,
  "year" integer not null,
  "total_days" numeric not null default('0'),
  "used_days" numeric not null default('0'),
  "pending_days" numeric not null default('0'),
  "carried_over_days" numeric not null default('0'),
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("employee_id") references employees("id") on delete cascade on update no action,
  foreign key("leave_type_id") references "leave_types"("id") on delete restrict,
  primary key("id")
);
CREATE INDEX "leave_balances_company_id_index" on "leave_balances"(
  "company_id"
);
CREATE INDEX "leave_balances_employee_id_index" on "leave_balances"(
  "employee_id"
);
CREATE UNIQUE INDEX "leave_balances_employee_id_leave_type_id_year_unique" on "leave_balances"(
  "employee_id",
  "leave_type_id",
  "year"
);
CREATE INDEX "leave_balances_leave_type_id_index" on "leave_balances"(
  "leave_type_id"
);
CREATE TABLE IF NOT EXISTS "onboarding_flows"(
  "id" varchar not null,
  "company_id" varchar not null,
  "employee_id" varchar not null,
  "template_id" varchar,
  "status" varchar not null default('not_started'),
  "started_at" datetime,
  "completed_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("employee_id") references employees("id") on delete cascade on update no action,
  foreign key("template_id") references "onboarding_templates"("id") on delete set null,
  primary key("id")
);
CREATE INDEX "onboarding_flows_company_id_index" on "onboarding_flows"(
  "company_id"
);
CREATE INDEX "onboarding_flows_employee_id_index" on "onboarding_flows"(
  "employee_id"
);
CREATE INDEX "onboarding_flows_template_id_index" on "onboarding_flows"(
  "template_id"
);
CREATE TABLE IF NOT EXISTS "onboarding_tasks"(
  "id" varchar not null,
  "company_id" varchar not null,
  "flow_id" varchar not null,
  "template_task_id" varchar,
  "title" varchar not null,
  "description" text,
  "task_type" varchar not null default('read_acknowledge'),
  "assigned_to_tenant_id" varchar,
  "due_date" date,
  "status" varchar not null default('pending'),
  "completed_at" datetime,
  "completion_notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("flow_id") references "onboarding_flows"("id") on delete cascade,
  primary key("id")
);
CREATE INDEX "onboarding_tasks_assigned_to_tenant_id_index" on "onboarding_tasks"(
  "assigned_to_tenant_id"
);
CREATE INDEX "onboarding_tasks_company_id_index" on "onboarding_tasks"(
  "company_id"
);
CREATE INDEX "onboarding_tasks_flow_id_index" on "onboarding_tasks"("flow_id");
CREATE INDEX "onboarding_tasks_template_task_id_index" on "onboarding_tasks"(
  "template_task_id"
);
CREATE TABLE IF NOT EXISTS "pay_runs"(
  "id" varchar not null,
  "company_id" varchar not null,
  "payroll_entity_id" varchar not null,
  "status" varchar not null default('draft'),
  "pay_frequency" varchar not null default('monthly'),
  "pay_period_start" date not null,
  "pay_period_end" date not null,
  "payment_date" date not null,
  "total_gross" numeric not null default('0'),
  "total_net" numeric not null default('0'),
  "total_deductions" numeric not null default('0'),
  "created_by_tenant_id" varchar,
  "approved_by_tenant_id" varchar,
  "approved_at" datetime,
  "processed_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("company_id") references "companies"("id") on delete cascade,
  primary key("id")
);
CREATE INDEX "pay_runs_approved_by_tenant_id_index" on "pay_runs"(
  "approved_by_tenant_id"
);
CREATE INDEX "pay_runs_company_id_index" on "pay_runs"("company_id");
CREATE INDEX "pay_runs_created_by_tenant_id_index" on "pay_runs"(
  "created_by_tenant_id"
);
CREATE INDEX "pay_runs_payroll_entity_id_index" on "pay_runs"(
  "payroll_entity_id"
);
CREATE TABLE IF NOT EXISTS "pay_run_employees"(
  "id" varchar not null,
  "company_id" varchar not null,
  "pay_run_id" varchar not null,
  "employee_id" varchar not null,
  "gross_pay" numeric not null default('0'),
  "net_pay" numeric not null default('0'),
  "total_deductions" numeric not null default('0'),
  "adjustments" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("pay_run_id") references pay_runs("id") on delete cascade on update no action,
  foreign key("employee_id") references "employees"("id") on delete cascade,
  primary key("id")
);
CREATE INDEX "pay_run_employees_company_id_index" on "pay_run_employees"(
  "company_id"
);
CREATE INDEX "pay_run_employees_employee_id_index" on "pay_run_employees"(
  "employee_id"
);
CREATE INDEX "pay_run_employees_pay_run_id_index" on "pay_run_employees"(
  "pay_run_id"
);
CREATE TABLE IF NOT EXISTS "pay_run_lines"(
  "id" varchar not null,
  "company_id" varchar not null,
  "pay_run_employee_id" varchar not null,
  "pay_element_id" varchar,
  "description" varchar not null,
  "amount" numeric not null,
  "is_deduction" tinyint(1) not null default('0'),
  "source" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("pay_run_employee_id") references "pay_run_employees"("id") on delete cascade,
  primary key("id")
);
CREATE INDEX "pay_run_lines_company_id_index" on "pay_run_lines"("company_id");
CREATE INDEX "pay_run_lines_pay_element_id_index" on "pay_run_lines"(
  "pay_element_id"
);
CREATE INDEX "pay_run_lines_pay_run_employee_id_index" on "pay_run_lines"(
  "pay_run_employee_id"
);
CREATE TABLE IF NOT EXISTS "payslips"(
  "id" varchar not null,
  "company_id" varchar not null,
  "pay_run_id" varchar not null,
  "employee_id" varchar not null,
  "pay_run_employee_id" varchar,
  "pdf_file_id" varchar,
  "generated_at" datetime,
  "sent_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "period_start" date,
  "period_end" date,
  "status" varchar not null default('generated'),
  "pdf_path" varchar,
  foreign key("pay_run_id") references pay_runs("id") on delete cascade on update no action,
  foreign key("employee_id") references "employees"("id") on delete cascade,
  primary key("id")
);
CREATE INDEX "payslips_company_id_index" on "payslips"("company_id");
CREATE INDEX "payslips_employee_id_index" on "payslips"("employee_id");
CREATE INDEX "payslips_pay_run_employee_id_index" on "payslips"(
  "pay_run_employee_id"
);
CREATE UNIQUE INDEX "payslips_pay_run_id_employee_id_unique" on "payslips"(
  "pay_run_id",
  "employee_id"
);
CREATE INDEX "payslips_pay_run_id_index" on "payslips"("pay_run_id");
CREATE INDEX "payslips_pdf_file_id_index" on "payslips"("pdf_file_id");
CREATE TABLE IF NOT EXISTS "salary_records"(
  "id" varchar not null,
  "company_id" varchar not null,
  "employee_id" varchar not null,
  "salary_encrypted" text not null,
  "currency" varchar not null default('EUR'),
  "pay_frequency" varchar not null default('monthly'),
  "effective_from" date not null,
  "effective_to" date,
  "notes" text,
  "created_by_tenant_id" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("employee_id") references "employees"("id") on delete cascade,
  primary key("id")
);
CREATE INDEX "salary_records_company_id_index" on "salary_records"(
  "company_id"
);
CREATE INDEX "salary_records_created_by_tenant_id_index" on "salary_records"(
  "created_by_tenant_id"
);
CREATE INDEX "salary_records_employee_id_index" on "salary_records"(
  "employee_id"
);
CREATE TABLE IF NOT EXISTS "tasks"(
  "id" varchar not null,
  "company_id" varchar not null,
  "title" varchar not null,
  "description" text,
  "parent_task_id" varchar,
  "priority" varchar not null default('p3_medium'),
  "status" varchar not null default('todo'),
  "assignee_tenant_id" varchar,
  "due_date" date,
  "start_date" date,
  "estimated_hours" numeric,
  "is_recurring" tinyint(1) not null default('0'),
  "recurrence_rule" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("company_id") references companies("id") on delete cascade on update no action,
  foreign key("assignee_tenant_id") references "tenants"("id") on delete set null,
  primary key("id")
);
CREATE INDEX "tasks_assignee_tenant_id_index" on "tasks"("assignee_tenant_id");
CREATE INDEX "tasks_company_id_index" on "tasks"("company_id");
CREATE INDEX "tasks_company_id_status_index" on "tasks"(
  "company_id",
  "status"
);
CREATE INDEX "tasks_parent_task_id_index" on "tasks"("parent_task_id");
CREATE TABLE IF NOT EXISTS "time_entries"(
  "id" varchar not null,
  "company_id" varchar not null,
  "tenant_id" varchar not null,
  "task_id" varchar,
  "description" varchar,
  "entry_date" date not null,
  "minutes" integer not null,
  "is_billable" tinyint(1) not null default('0'),
  "is_approved" tinyint(1) not null default('0'),
  "approved_by_tenant_id" varchar,
  "approved_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("tenant_id") references tenants("id") on delete cascade on update no action,
  foreign key("task_id") references "tasks"("id") on delete set null,
  primary key("id")
);
CREATE INDEX "time_entries_approved_by_tenant_id_index" on "time_entries"(
  "approved_by_tenant_id"
);
CREATE INDEX "time_entries_company_id_index" on "time_entries"("company_id");
CREATE INDEX "time_entries_task_id_index" on "time_entries"("task_id");
CREATE INDEX "time_entries_tenant_id_index" on "time_entries"("tenant_id");
CREATE TABLE IF NOT EXISTS "timesheets"(
  "id" varchar not null,
  "company_id" varchar not null,
  "tenant_id" varchar not null,
  "week_start_date" date not null,
  "status" varchar not null default('draft'),
  "submitted_at" datetime,
  "total_minutes" integer not null default('0'),
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("tenant_id") references tenants("id") on delete cascade on update no action,
  foreign key("company_id") references "companies"("id") on delete cascade,
  primary key("id")
);
CREATE INDEX "timesheets_company_id_index" on "timesheets"("company_id");
CREATE INDEX "timesheets_tenant_id_index" on "timesheets"("tenant_id");
CREATE UNIQUE INDEX "timesheets_tenant_id_week_start_date_unique" on "timesheets"(
  "tenant_id",
  "week_start_date"
);
CREATE TABLE IF NOT EXISTS "document_folders"(
  "id" varchar not null,
  "company_id" varchar not null,
  "name" varchar not null,
  "parent_folder_id" varchar,
  "created_by_tenant_id" varchar,
  "is_system" tinyint(1) not null default('0'),
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("company_id") references "companies"("id") on delete cascade,
  primary key("id")
);
CREATE INDEX "document_folders_company_id_index" on "document_folders"(
  "company_id"
);
CREATE INDEX "document_folders_parent_folder_id_index" on "document_folders"(
  "parent_folder_id"
);
CREATE TABLE IF NOT EXISTS "documents"(
  "id" varchar not null,
  "company_id" varchar not null,
  "folder_id" varchar,
  "current_file_id" varchar,
  "title" varchar not null,
  "original_filename" varchar,
  "mime_type" varchar,
  "file_size_bytes" integer,
  "version_number" integer not null default('1'),
  "uploaded_by_tenant_id" varchar,
  "is_starred" tinyint(1) not null default('0'),
  "tags" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("folder_id") references document_folders("id") on delete set null on update no action,
  foreign key("company_id") references companies("id") on delete cascade on update no action,
  primary key("id")
);
CREATE INDEX "documents_company_id_index" on "documents"("company_id");
CREATE INDEX "documents_current_file_id_index" on "documents"(
  "current_file_id"
);
CREATE INDEX "documents_folder_id_index" on "documents"("folder_id");
CREATE INDEX "documents_uploaded_by_tenant_id_index" on "documents"(
  "uploaded_by_tenant_id"
);
CREATE TABLE IF NOT EXISTS "demo_requests"(
  "id" varchar not null,
  "first_name" varchar not null,
  "last_name" varchar not null,
  "email" varchar not null,
  "company_name" varchar not null,
  "company_size" varchar not null,
  "modules_interested" text,
  "heard_from" varchar,
  "notes" text,
  "phone" varchar,
  "ip_address" varchar,
  "user_agent" text,
  "utm_source" varchar,
  "utm_medium" varchar,
  "utm_campaign" varchar,
  "utm_content" varchar,
  "utm_term" varchar,
  "status" varchar not null default 'new',
  "assigned_to" varchar,
  "scheduled_at" datetime,
  "notes_internal" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "blog_categories"(
  "id" varchar not null,
  "name" varchar not null,
  "slug" varchar not null,
  "description" text,
  "display_order" integer not null default '0',
  "is_published" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE UNIQUE INDEX "blog_categories_slug_unique" on "blog_categories"("slug");
CREATE TABLE IF NOT EXISTS "blog_posts"(
  "id" varchar not null,
  "blog_category_id" varchar not null,
  "title" varchar not null,
  "slug" varchar not null,
  "excerpt" text,
  "featured_image" varchar,
  "body" text not null,
  "author_id" varchar,
  "tags" text,
  "status" varchar not null default 'draft',
  "published_at" datetime,
  "seo_title" varchar,
  "seo_description" text,
  "og_image" varchar,
  "seo_noindex" tinyint(1) not null default '0',
  "reading_time" integer,
  "cta_type" varchar not null default 'demo',
  "cta_module" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("blog_category_id") references "blog_categories"("id") on delete cascade,
  primary key("id")
);
CREATE UNIQUE INDEX "blog_posts_slug_unique" on "blog_posts"("slug");
CREATE TABLE IF NOT EXISTS "testimonials"(
  "id" varchar not null,
  "name" varchar not null,
  "role" varchar not null,
  "company" varchar not null,
  "quote" text not null,
  "photo" varchar,
  "is_featured" tinyint(1) not null default '0',
  "display_order" integer not null default '0',
  "is_published" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "newsletter_subscribers"(
  "id" varchar not null,
  "email" varchar not null,
  "status" varchar not null default 'subscribed',
  "source" varchar,
  "subscribed_at" datetime,
  "unsubscribed_at" datetime,
  "double_opt_in_confirmed" tinyint(1) not null default '0',
  "double_opt_in_sent_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE UNIQUE INDEX "newsletter_subscribers_email_unique" on "newsletter_subscribers"(
  "email"
);
CREATE TABLE IF NOT EXISTS "faq_entries"(
  "id" varchar not null,
  "question" text not null,
  "answer" text not null,
  "context" varchar not null default 'general',
  "display_order" integer not null default '0',
  "is_published" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "team_members"(
  "id" varchar not null,
  "name" varchar not null,
  "role" varchar not null,
  "bio" varchar,
  "photo" varchar,
  "linkedin_url" varchar,
  "twitter_url" varchar,
  "display_order" integer not null default '0',
  "is_published" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "open_roles"(
  "id" varchar not null,
  "title" varchar not null,
  "slug" varchar not null,
  "department" varchar not null,
  "location" varchar not null,
  "type" varchar not null,
  "salary_range" varchar,
  "about_role" text not null,
  "responsibilities" text not null,
  "requirements" text not null,
  "nice_to_have" text,
  "how_to_apply" text not null,
  "status" varchar not null default 'open',
  "published_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE UNIQUE INDEX "open_roles_slug_unique" on "open_roles"("slug");
CREATE TABLE IF NOT EXISTS "changelog_entries"(
  "id" varchar not null,
  "title" varchar not null,
  "type" varchar not null,
  "body" text not null,
  "screenshot" varchar,
  "docs_url" varchar,
  "published_at" datetime,
  "is_published" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "help_categories"(
  "id" varchar not null,
  "name" varchar not null,
  "slug" varchar not null,
  "description" text,
  "icon" varchar,
  "parent_id" varchar,
  "display_order" integer not null default '0',
  "is_published" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);
CREATE UNIQUE INDEX "help_categories_slug_unique" on "help_categories"("slug");
CREATE TABLE IF NOT EXISTS "help_articles"(
  "id" varchar not null,
  "help_category_id" varchar not null,
  "title" varchar not null,
  "slug" varchar not null,
  "body" text not null,
  "seo_title" varchar,
  "seo_description" text,
  "is_published" tinyint(1) not null default '0',
  "last_reviewed_at" datetime,
  "helpful_count" integer not null default '0',
  "not_helpful_count" integer not null default '0',
  "module_link" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("help_category_id") references "help_categories"("id") on delete cascade,
  primary key("id")
);
CREATE UNIQUE INDEX "help_articles_slug_unique" on "help_articles"("slug");
CREATE TABLE IF NOT EXISTS "contact_submissions"(
  "id" varchar not null,
  "name" varchar not null,
  "email" varchar not null,
  "subject" varchar not null,
  "message" text not null,
  "status" varchar not null default 'new',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  primary key("id")
);

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2025_08_14_170933_add_two_factor_columns_to_users_table',1);
INSERT INTO migrations VALUES(5,'2026_05_05_082158_create_companies_table',1);
INSERT INTO migrations VALUES(6,'2026_05_05_082158_create_tenants_table',1);
INSERT INTO migrations VALUES(7,'2026_05_05_082650_create_addresses_table',1);
INSERT INTO migrations VALUES(8,'2026_05_05_130000_create_permission_tables',1);
INSERT INTO migrations VALUES(9,'2026_05_05_200000_create_modules_table',1);
INSERT INTO migrations VALUES(10,'2026_05_05_200001_create_sub_modules_table',1);
INSERT INTO migrations VALUES(11,'2026_05_05_200002_create_company_module_table',1);
INSERT INTO migrations VALUES(12,'2026_05_05_204443_create_activity_log_table',1);
INSERT INTO migrations VALUES(13,'2026_05_05_220000_update_company_locale_to_language_code',1);
INSERT INTO migrations VALUES(14,'2026_05_06_053134_add_currency_to_companies_table',2);
INSERT INTO migrations VALUES(15,'2026_05_06_100000_create_notifications_table',3);
INSERT INTO migrations VALUES(16,'2026_05_06_100001_create_notification_preferences_table',3);
INSERT INTO migrations VALUES(17,'2026_05_06_100002_create_api_keys_table',3);
INSERT INTO migrations VALUES(18,'2026_05_06_100003_create_files_table',3);
INSERT INTO migrations VALUES(19,'2026_05_06_100004_add_logo_to_companies_table',3);
INSERT INTO migrations VALUES(20,'2026_05_06_200000_create_departments_table',4);
INSERT INTO migrations VALUES(21,'2026_05_06_200001_create_employees_table',4);
INSERT INTO migrations VALUES(22,'2026_05_06_200002_create_employee_documents_table',4);
INSERT INTO migrations VALUES(23,'2026_05_06_200003_create_employee_custom_fields_table',4);
INSERT INTO migrations VALUES(24,'2026_05_06_200004_create_employee_custom_field_values_table',4);
INSERT INTO migrations VALUES(25,'2026_05_06_210000_create_onboarding_templates_table',4);
INSERT INTO migrations VALUES(26,'2026_05_06_210001_create_onboarding_template_tasks_table',4);
INSERT INTO migrations VALUES(27,'2026_05_06_210002_create_onboarding_flows_table',4);
INSERT INTO migrations VALUES(28,'2026_05_06_210003_create_onboarding_tasks_table',4);
INSERT INTO migrations VALUES(29,'2026_05_06_210004_create_onboarding_checkins_table',4);
INSERT INTO migrations VALUES(30,'2026_05_06_210005_create_onboarding_checkin_responses_table',4);
INSERT INTO migrations VALUES(31,'2026_05_06_220000_create_leave_types_table',4);
INSERT INTO migrations VALUES(32,'2026_05_06_220001_create_leave_policies_table',4);
INSERT INTO migrations VALUES(33,'2026_05_06_220002_create_leave_balances_table',4);
INSERT INTO migrations VALUES(34,'2026_05_06_220003_create_leave_requests_table',4);
INSERT INTO migrations VALUES(35,'2026_05_06_220004_create_public_holidays_table',4);
INSERT INTO migrations VALUES(36,'2026_05_06_230000_create_payroll_entities_table',4);
INSERT INTO migrations VALUES(37,'2026_05_06_230001_create_pay_elements_table',4);
INSERT INTO migrations VALUES(38,'2026_05_06_230002_create_tax_configurations_table',4);
INSERT INTO migrations VALUES(39,'2026_05_06_230003_create_salary_records_table',4);
INSERT INTO migrations VALUES(40,'2026_05_06_230004_create_pay_runs_table',4);
INSERT INTO migrations VALUES(41,'2026_05_06_230005_create_pay_run_employees_table',4);
INSERT INTO migrations VALUES(42,'2026_05_06_230006_create_pay_run_lines_table',4);
INSERT INTO migrations VALUES(43,'2026_05_06_230007_create_payslips_table',4);
INSERT INTO migrations VALUES(44,'2026_05_06_230008_create_deductions_table',4);
INSERT INTO migrations VALUES(45,'2026_05_06_230009_create_contractor_payments_table',4);
INSERT INTO migrations VALUES(46,'2026_05_06_240000_create_task_labels_table',4);
INSERT INTO migrations VALUES(47,'2026_05_06_240001_create_tasks_table',4);
INSERT INTO migrations VALUES(48,'2026_05_06_240002_create_task_label_assignments_table',4);
INSERT INTO migrations VALUES(49,'2026_05_06_240003_create_task_dependencies_table',4);
INSERT INTO migrations VALUES(50,'2026_05_06_240004_create_task_automations_table',4);
INSERT INTO migrations VALUES(51,'2026_05_06_240005_create_task_automation_logs_table',4);
INSERT INTO migrations VALUES(52,'2026_05_06_250000_create_time_entries_table',4);
INSERT INTO migrations VALUES(53,'2026_05_06_250001_create_timesheets_table',4);
INSERT INTO migrations VALUES(54,'2026_05_06_250002_create_timesheet_approvals_table',4);
INSERT INTO migrations VALUES(55,'2026_05_06_260000_create_document_folders_table',4);
INSERT INTO migrations VALUES(56,'2026_05_06_260001_create_documents_table',4);
INSERT INTO migrations VALUES(57,'2026_05_06_260002_create_document_versions_table',4);
INSERT INTO migrations VALUES(58,'2026_05_06_260003_create_document_shares_table',4);
INSERT INTO migrations VALUES(59,'2026_05_07_300000_fix_payslips_add_missing_columns',4);
INSERT INTO migrations VALUES(60,'2026_05_07_300001_fix_payslips_add_unique_pay_run_employee',4);
INSERT INTO migrations VALUES(61,'2026_05_07_300002_fix_employees_employee_number_per_company_unique',4);
INSERT INTO migrations VALUES(62,'2026_05_07_300003_fix_timesheets_add_unique_tenant_week',4);
INSERT INTO migrations VALUES(63,'2026_05_07_300004_fix_task_dependencies_add_unique_pair',4);
INSERT INTO migrations VALUES(64,'2026_05_07_300005_fix_task_label_assignments_drop_bigincrement_id',5);
INSERT INTO migrations VALUES(65,'2026_05_07_300006_fix_add_foreign_key_constraints',5);
INSERT INTO migrations VALUES(66,'2026_05_07_400000_add_soft_deletes_to_leave_balances_table',5);
INSERT INTO migrations VALUES(67,'2026_05_07_400001_add_soft_deletes_to_pay_run_employees_table',5);
INSERT INTO migrations VALUES(68,'2026_05_07_400002_add_soft_deletes_to_pay_run_lines_table',5);
INSERT INTO migrations VALUES(69,'2026_05_07_400003_add_soft_deletes_to_employee_custom_field_values_table',5);
INSERT INTO migrations VALUES(70,'2026_05_07_400004_add_soft_deletes_to_onboarding_checkin_responses_table',5);
INSERT INTO migrations VALUES(71,'2026_05_07_400005_add_soft_deletes_to_timesheet_approvals_table',5);
INSERT INTO migrations VALUES(72,'2026_05_07_400006_add_soft_deletes_to_task_dependencies_table',5);
INSERT INTO migrations VALUES(73,'2026_05_07_400007_add_soft_deletes_to_task_automation_logs_table',5);
INSERT INTO migrations VALUES(74,'2026_05_07_400008_add_soft_deletes_to_document_versions_table',5);
INSERT INTO migrations VALUES(75,'2026_05_07_400009_add_soft_deletes_to_notification_preferences_table',5);
INSERT INTO migrations VALUES(76,'2026_05_07_500000_fix_documents_original_filename_nullable',5);
INSERT INTO migrations VALUES(77,'2026_05_07_600000_create_demo_requests_table',6);
INSERT INTO migrations VALUES(78,'2026_05_07_600001_create_blog_categories_table',6);
INSERT INTO migrations VALUES(79,'2026_05_07_600002_create_blog_posts_table',6);
INSERT INTO migrations VALUES(80,'2026_05_07_600003_create_testimonials_table',6);
INSERT INTO migrations VALUES(81,'2026_05_07_600004_create_newsletter_subscribers_table',6);
INSERT INTO migrations VALUES(82,'2026_05_07_600005_create_faq_entries_table',6);
INSERT INTO migrations VALUES(83,'2026_05_07_600006_create_team_members_table',6);
INSERT INTO migrations VALUES(84,'2026_05_07_600007_create_open_roles_table',6);
INSERT INTO migrations VALUES(85,'2026_05_07_600008_create_changelog_entries_table',6);
INSERT INTO migrations VALUES(86,'2026_05_07_600009_create_help_categories_table',6);
INSERT INTO migrations VALUES(87,'2026_05_07_600010_create_help_articles_table',6);
INSERT INTO migrations VALUES(88,'2026_05_07_600011_create_contact_submissions_table',6);
