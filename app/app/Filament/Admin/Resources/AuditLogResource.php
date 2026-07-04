<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AuditLogResource\Pages\ListAuditLogs;
use App\Models\Activity;
use App\Models\Company;
use App\Support\Scopes\CompanyScope;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Cross-company audit view for FlowFlex staff (core.audit-log/log-browser).
 * The ONLY sanctioned CompanyScope bypass outside Support/ — admin guard,
 * never exposed in a company panel (security.md).
 */
class AuditLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Audit log';

    protected static ?string $modelLabel = 'audit entry';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScope(CompanyScope::class)
            ->with(['causer', 'company']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('When')->dateTime('d M Y · H:i')->sortable(),
                TextColumn::make('company.name')->label('Company')->sortable(),
                TextColumn::make('log_name')->label('Domain')->badge(),
                TextColumn::make('description')->label('Action')->searchable()->limit(60),
                TextColumn::make('causer.full_name')->label('By')->default('System'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('company_id')
                    ->label('Company')
                    ->options(fn (): array => Company::query()->pluck('name', 'id')->all()),
                SelectFilter::make('log_name')
                    ->label('Domain')
                    ->options(fn (): array => Activity::query()
                        ->withoutGlobalScope(CompanyScope::class)
                        ->distinct()
                        ->pluck('log_name', 'log_name')
                        ->filter()
                        ->all()),
            ])
            ->emptyStateHeading('No activity yet')
            ->emptyStateDescription('Tenant activity across all companies lands here.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditLogs::route('/'),
        ];
    }
}
