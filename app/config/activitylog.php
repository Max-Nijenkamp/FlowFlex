<?php

declare(strict_types=1);

return [

    'enabled' => env('ACTIVITYLOG_ENABLED', true),

    // Retention is enforced per-company by PruneAuditLogCommand, not this
    // global default — kept for the vendor clean command's sake only.
    'clean_after_days' => 730,

    'default_log_name' => 'default',

    'default_auth_driver' => null,

    'include_soft_deleted_subjects' => false,

    // Tenant-scoped model: company_id force-set from CompanyContext.
    'activity_model' => App\Models\Activity::class,

    'default_except_attributes' => [],

    'table_name' => env('ACTIVITYLOG_TABLE_NAME', 'activity_log'),

    'database_connection' => env('ACTIVITYLOG_DB_CONNECTION'),
];
