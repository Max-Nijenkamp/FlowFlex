<?php

declare(strict_types=1);

namespace App\Filament\Hr\Widgets;

use App\Models\HR\Employee;
use App\Support\Services\CompanyContext;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class DepartmentBreakdownWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $ctx = app(CompanyContext::class);
        $companyId = $ctx->hasCompany() ? $ctx->currentId() : null;

        return $table
            ->query(
                Employee::withoutGlobalScopes()
                    ->selectRaw('department, count(*) as headcount')
                    ->where('company_id', $companyId)
                    ->where('status', 'active')
                    ->whereNotNull('department')
                    ->groupBy('department')
                    ->orderByDesc('headcount')
            )
            ->columns([
                TextColumn::make('department')->label('Department'),
                TextColumn::make('headcount')->label('Headcount')->sortable(),
            ])
            ->heading('Department Breakdown')
            ->paginated(false);
    }
}
