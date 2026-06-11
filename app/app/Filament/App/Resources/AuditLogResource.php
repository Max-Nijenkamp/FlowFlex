<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Contracts\Core\BillingServiceInterface;
use App\Models\Core\Activity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Read-only audit trail browser. No create/edit/delete — rows are written
 * exclusively by AuditLogger::log().
 */
class AuditLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $modelLabel = 'audit entry';

    protected static ?string $pluralModelLabel = 'audit log';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('core.audit.view-any')
            && app(BillingServiceInterface::class)->hasModule('core.audit');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('log_name')->badge()->label('Domain'),
                TextColumn::make('description')->searchable()->wrap(),
                TextColumn::make('causer.email')->label('By')->placeholder('system'),
                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '—'),
            ])
            ->filters([
                SelectFilter::make('log_name')->label('Domain')
                    ->options(fn () => Activity::query()->distinct()->pluck('log_name', 'log_name')->filter()->all()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => AuditLogResource\Pages\ListAuditLogs::route('/'),
        ];
    }
}
