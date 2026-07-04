<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Models\Activity;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

/** Latest workspace activity on the dashboard (reads the audit log). */
class RecentActivityWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent activity')
            ->query(fn (): Builder => Activity::query()->with('causer')->latest())
            ->defaultPaginationPageOption(5)
            ->paginated([5])
            ->columns([
                TextColumn::make('created_at')->label('When')->since(),
                TextColumn::make('description')->label('Action'),
                TextColumn::make('causer.full_name')->label('By')->default('System'),
            ])
            ->emptyStateHeading('No activity yet')
            ->emptyStateDescription('Changes made in your workspace will show up here.');
    }
}
