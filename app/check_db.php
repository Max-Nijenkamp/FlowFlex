<?php
echo 'users: ' . \App\Models\User::withoutGlobalScopes()->count() . "\n";
echo 'admins: ' . \App\Models\Admin::count() . "\n";
echo 'companies: ' . \App\Models\Company::count() . "\n";
echo 'db: ' . config('database.default') . ' / ' . config('database.connections.pgsql.database') . "\n";
