<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\AuditLogResource\Pages\ListAuditLogs;
use App\Models\Activity;
use App\Models\User;
use App\Services\BillingService;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Read-only log browser over activity_log (core.audit-log/log-browser).
 * AuditLogger owns all writes — no create/edit/delete surface exists.
 */
class AuditLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Audit log';

    protected static ?string $modelLabel = 'audit entry';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('core.audit.view-any')
            && app(BillingService::class)->hasModule('core.audit');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('causer');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime('d M Y · H:i')
                    ->sortable(),
                TextColumn::make('log_name')
                    ->label('Domain')
                    ->badge(),
                TextColumn::make('description')
                    ->label('Action')
                    ->searchable()
                    ->limit(60),
                TextColumn::make('causer.full_name')
                    ->label('By')
                    ->default('System'),
                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(fn (?string $state, Activity $record): string => $state === null
                        ? '—'
                        : class_basename($state).' #'.substr((string) $record->subject_id, -6)),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Domain')
                    ->options(fn (): array => Activity::query()
                        ->distinct()
                        ->pluck('log_name', 'log_name')
                        ->filter()
                        ->all()),
                SelectFilter::make('event')
                    ->label('Action type')
                    ->options(fn (): array => Activity::query()
                        ->distinct()
                        ->pluck('event', 'event')
                        ->filter()
                        ->all()),
                SelectFilter::make('causer_id')
                    ->label('User')
                    ->options(fn (): array => User::query()
                        ->get()
                        ->mapWithKeys(fn (User $user): array => [$user->id => $user->full_name])
                        ->all()),
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, string $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, string $date) => $q->whereDate('created_at', '<=', $date))),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalHeading('Audit entry')
                    ->modalWidth('lg')
                    ->modalContent(fn (Activity $record) => view('filament.app.audit-entry', ['record' => $record])),
            ])
            ->emptyStateHeading('No activity yet')
            ->emptyStateDescription('Every change in your workspace will show up here — who did what, and when.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditLogs::route('/'),
        ];
    }
}
