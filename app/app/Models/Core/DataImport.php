<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\States\Core\DataImport\DataImportState;
use App\Support\Traits\BelongsToCompany;
use Database\Factories\Core\DataImportFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\ModelStates\HasStates;

/**
 * @property string $id
 * @property string $company_id
 * @property string $target
 * @property string $filename
 * @property DataImportState $status
 * @property array<string, string> $column_map
 * @property int $total_rows
 * @property int $success_rows
 * @property int $error_rows
 * @property string|null $stored_path
 * @property string|null $error_report_path
 * @property string $imported_by
 */
class DataImport extends Model
{
    /** @use HasFactory<DataImportFactory> */
    use BelongsToCompany, HasFactory, HasStates, HasUlids, SoftDeletes;

    protected $fillable = [
        'company_id',
        'target',
        'filename',
        'status',
        'column_map',
        'total_rows',
        'success_rows',
        'error_rows',
        'stored_path',
        'error_report_path',
        'imported_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => DataImportState::class,
            'column_map' => 'array',
            'total_rows' => 'integer',
            'success_rows' => 'integer',
            'error_rows' => 'integer',
        ];
    }
}
