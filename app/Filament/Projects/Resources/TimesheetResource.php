<?php

namespace App\Filament\Projects\Resources;

use App\Enums\Projects\TimesheetStatus;
use App\Filament\Projects\Enums\NavigationGroup;
use App\Filament\Projects\Resources\TimesheetResource\Pages\CreateTimesheet;
use App\Filament\Projects\Resources\TimesheetResource\Pages\EditTimesheet;
use App\Filament\Projects\Resources\TimesheetResource\Pages\ListTimesheets;
use App\Models\Projects\Timesheet;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TimesheetResource extends Resource
{
    protected static ?string $model = Timesheet::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-table-cells';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::TimeTracking->label();
    }

    public static function getModelLabel(): string
    {
        return __('projects.resources.timesheets.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('projects.resources.timesheets.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('projects.timesheets.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('projects.timesheets.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('projects.timesheets.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('projects.timesheets.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('projects.resources.timesheets.sections.details'))
                ->schema([
                    DatePicker::make('week_start_date')
                        ->label(__('projects.resources.timesheets.fields.week_start_date'))
                        ->required()
                        ->native(false),

                    Select::make('status')
                        ->options(
                            collect(TimesheetStatus::cases())
                                ->mapWithKeys(fn (TimesheetStatus $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->default(TimesheetStatus::Draft->value)
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('week_start_date')
                    ->label(__('projects.resources.timesheets.columns.week_start_date'))
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?TimesheetStatus $state) => $state?->label())
                    ->color(fn (?TimesheetStatus $state) => $state?->color()),

                TextColumn::make('submitted_at')
                    ->label(__('projects.resources.timesheets.columns.submitted_at'))
                    ->dateTime('d M Y H:i')
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('week_start_date', 'desc')
            ->striped()
            ->filters([
                SelectFilter::make('status')
                    ->options(
                        collect(TimesheetStatus::cases())
                            ->mapWithKeys(fn (TimesheetStatus $case) => [$case->value => $case->label()])
                            ->toArray()
                    ),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTimesheets::route('/'),
            'create' => CreateTimesheet::route('/create'),
            'edit'   => EditTimesheet::route('/{record}/edit'),
        ];
    }
}
