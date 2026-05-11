<?php

declare(strict_types=1);

namespace App\Models\Projects;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KanbanColumn extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use HasUlids;

    protected $table = 'kanban_columns';

    protected $fillable = [
        'company_id',
        'board_id',
        'name',
        'color',
        'wip_limit',
        'maps_to_status',
        'sort_order',
    ];

    public function board(): BelongsTo
    {
        return $this->belongsTo(KanbanBoard::class, 'board_id');
    }
}
