<?php

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Enums\NavigationGroup;
use App\Filament\Hr\Resources\DepartmentResource\Pages\CreateDepartment;
use App\Filament\Hr\Resources\DepartmentResource\Pages\EditDepartment;
use App\Filament\Hr\Resources\DepartmentResource\Pages\ListDepartments;
use App\Models\Hr\Department;
use App\Models\Hr\Employee;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::People->label();
    }

    public static function getModelLabel(): string
    {
        return __('hr.resources.departments.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('hr.resources.departments.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr.departments.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr.departments.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('hr.departments.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('hr.departments.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('hr.resources.departments.sections.details'))
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    Textarea::make('description')
                        ->nullable()
                        ->rows(4),

                    Select::make('parent_department_id')
                        ->label(__('hr.resources.departments.fields.parent_department'))
                        ->options(function ($record) {
                            $query = Department::query();
                            if ($record?->id) {
                                $query->where('id', '!=', $record->id);
                            }

                            return $query->pluck('name', 'id')->toArray();
                        })
                        ->nullable()
                        ->searchable(),

                    Select::make('manager_id')
                        ->label(__('hr.resources.departments.fields.manager'))
                        ->relationship('manager', 'first_name')
                        ->getOptionLabelFromRecordUsing(fn (Employee $record) => trim("{$record->first_name} {$record->last_name}"))
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->sortable(),

                TextColumn::make('parentDepartment.name')
                    ->label(__('hr.resources.departments.columns.parent'))
                    ->placeholder('—'),

                TextColumn::make('manager_name')
                    ->label(__('hr.resources.departments.columns.manager'))
                    ->getStateUsing(fn (Department $record) => $record->manager
                        ? trim("{$record->manager->first_name} {$record->manager->last_name}")
                        : null
                    )
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->striped()
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
        return parent::getEloquentQuery()->with(['manager', 'parentDepartment']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListDepartments::route('/'),
            'create' => CreateDepartment::route('/create'),
            'edit'   => EditDepartment::route('/{record}/edit'),
        ];
    }
}
