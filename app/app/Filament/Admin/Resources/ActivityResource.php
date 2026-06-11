<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ActivityResource\Pages;
use App\Models\Activity;
use App\Models\Company;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/** Cross-company audit trail — read-only (core.staff-console monitoring). */
class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Monitoring';

    protected static ?string $modelLabel = 'audit event';

    public static function canAccess(): bool
    {
        return Auth::guard('admin')->check();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('log_name')->badge(),
                TextColumn::make('description')->searchable()->limit(80),
                TextColumn::make('subject_type')->label('Subject')
                    ->formatStateUsing(fn (?string $state): string => $state !== null ? class_basename($state) : '—'),
                TextColumn::make('company_id')->label('Company')
                    ->state(function (Activity $r): string {
                        if ($r->company_id === null) {
                            return '—';
                        }

                        $company = Company::query()->find($r->company_id);

                        return $company instanceof Company ? $company->name : $r->company_id;
                    }),
            ])
            ->filters([
                SelectFilter::make('log_name')->label('Log')
                    ->options(fn () => Activity::query()->distinct()->pluck('log_name', 'log_name')->filter()->all()),
                SelectFilter::make('company_id')->label('Company')
                    ->options(fn () => Company::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
        ];
    }
}
