<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function (User $user, string $id) {
    return $user->id === $id;
});

// Company-wide notification channel (ui-strategy row #10) — members only.
Broadcast::channel('company.{companyId}.notifications', function (User $user, string $companyId) {
    return $user->company_id === $companyId;
});

// Pipeline board collaboration (ui-strategy row #3).
Broadcast::channel('company.{companyId}.pipeline', function (User $user, string $companyId) {
    return $user->company_id === $companyId && $user->can('crm.pipeline.view');
});
