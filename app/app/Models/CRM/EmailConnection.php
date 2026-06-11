<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class EmailConnection extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'crm_email_connections';

    protected $fillable = ['company_id', 'user_id', 'provider', 'oauth_token', 'email_address', 'sync_enabled', 'default_visibility', 'last_synced_at'];

    protected function casts(): array
    {
        return ['oauth_token' => 'encrypted', 'sync_enabled' => 'boolean', 'last_synced_at' => 'datetime'];
    }
}
