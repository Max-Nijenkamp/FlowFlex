<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Staff-provisioned owner invites (core.staff-console) have no tenant
        // sender. Fresh installs get this from the amended create migration.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE user_invitations ALTER COLUMN invited_by DROP NOT NULL');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE user_invitations ALTER COLUMN invited_by SET NOT NULL');
        }
    }
};
