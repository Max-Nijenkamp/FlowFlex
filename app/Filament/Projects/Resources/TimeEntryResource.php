<?php

namespace App\Filament\Projects\Resources;

use App\Filament\Projects\Enums\NavigationGroup;
use App\Filament\Projects\Resources\TimeEntryResource\Pages\CreateTimeEntry;
use App\Filament\Projects\Resources\TimeEntryResource\Pages\EditTimeEntry;
use App\Filament\Projects\Resources\TimeEntryResource\Pages\ListTimeEntries;
use App\Models\Projects\TimeEntry;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TimeEntryResource extends Resource
{
    protected static ?string $model = TimeEntry::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::TimeTracking->label();
    }

    public static function getModelLabel(): string
    {
        return __('projects.resources.time_entries.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('projects.resources.time_entries.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('projects.time.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('projects.time.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('projects.time.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('projects.time.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('projects.resources.time_entries.sections.details'))
                ->schema([
                    Select::make('task_id')
                        ->label(__('projects.resources.time_entries.fields.task'))
                        ->relationship('task', 'title')
                        ->nullable()
                        ->searchable()
                        ->preload(),

                    DatePicker::make('entry_date')
                        ->required()
                        ->native(false)
                        ->default(now()),

                    Textarea::make('description')
                        ->nullable()
                        ->rows(3),

                    TextInput::make('minutes')
                        ->label(__('projects.resources.time_entries.fields.minutes'))
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->helperText('60 = 1 hour'),

                    Toggle::make('is_billable')
                        ->label(__('projects.resources.time_entries.fields.is_billable'))
                        ->default(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('entry_date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('task.title')
                    ->label(__('projects.resources.time_entries.columns.task'))
                    ->placeholder('—')
                    ->limit(40),

                TextColumn::make('description')
                    ->limit(50)
                    ->placeholder('—'),

                TextColumn::make('minutes')
                    ->label(__('projects.resources.time_entries.columns.duration'))
                    ->formatStateUsing(function (int $state): string {
                        $hours   = intdiv($state, 60);
                        $minutes = $state % 60;

                        return $minutes > 0
                            ? "{$hours}h {$minutes}m"
                            : "{$hours}h";
                    }),

                IconColumn::make('is_billable')
                    ->label(__('projects.resources.time_entries.columns.is_billable'))
                    ->boolean(),

                IconColumn::make('is_approved')
                    ->label(__('projects.resources.time_entries.columns.is_approved'))
                    ->boolean(),
            ])
            ->defaultSort('entry_date', 'desc')
            ->striped()
            ->filters([
                TernaryFilter::make('is_billable')
                    ->label(__('projects.resources.time_entries.filters.is_billable'))
                    ->trueLabel(__('projects.resources.time_entries.filters.billable_only'))
                    ->falseLabel(__('projects.resources.time_entries.filters.non_billable_only'))
                    ->placeholder(__('projects.resources.time_entries.filters.all_entries')),

                TernaryFilter::make('is_approved')
                    ->label(__('projects.resources.time_entries.filters.is_approved'))
                    ->trueLabel(__('projects.resources.time_entries.filters.approved_only'))
                    ->falseLabel(__('projects.resources.time_entries.filters.pending_only'))
                    ->placeholder(__('projects.resources.time_entries.filters.all_entries')),
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['task']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTimeEntries::route('/'),
            'create' => CreateTimeEntry::route('/create'),
            'edit'   => EditTimeEntry::route('/{record}/edit'),
        ];
    }
}
