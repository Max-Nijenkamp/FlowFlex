<?php

namespace App\Filament\Hr\Resources;

use App\Enums\Hr\EmploymentStatus;
use App\Enums\Hr\EmploymentType;
use App\Filament\Hr\Enums\NavigationGroup;
use App\Filament\Hr\Resources\EmployeeResource\Pages\CreateEmployee;
use App\Filament\Hr\Resources\EmployeeResource\Pages\EditEmployee;
use App\Filament\Hr\Resources\EmployeeResource\Pages\ListEmployees;
use App\Filament\Hr\Resources\EmployeeResource\RelationManagers\DocumentsRelationManager;
use App\Models\Hr\Department;
use App\Models\Hr\Employee;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::People->label();
    }

    public static function getModelLabel(): string
    {
        return __('hr.resources.employees.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('hr.resources.employees.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr.employees.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr.employees.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('hr.employees.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('hr.employees.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('hr.resources.employees.sections.personal_details'))
                ->columns(3)
                ->schema([
                    TextInput::make('first_name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('last_name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('middle_name')
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->email()
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('phone')
                        ->tel()
                        ->nullable()
                        ->maxLength(50),

                    DatePicker::make('date_of_birth')
                        ->nullable()
                        ->native(false),
                ]),

            Section::make(__('hr.resources.employees.sections.employment'))
                ->columns(2)
                ->schema([
                    TextInput::make('employee_number')
                        ->nullable()
                        ->maxLength(50),

                    Select::make('department_id')
                        ->label(__('hr.resources.employees.fields.department'))
                        ->options(fn () => Department::query()->pluck('name', 'id')->toArray())
                        ->nullable()
                        ->searchable(),

                    TextInput::make('job_title')
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('location')
                        ->nullable()
                        ->maxLength(255),

                    Select::make('manager_id')
                        ->label(__('hr.resources.employees.fields.manager'))
                        ->relationship('manager', 'first_name')
                        ->getOptionLabelFromRecordUsing(fn (Employee $record) => trim("{$record->first_name} {$record->last_name}"))
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Select::make('employment_type')
                        ->options(
                            collect(EmploymentType::cases())
                                ->mapWithKeys(fn (EmploymentType $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->nullable(),

                    Select::make('employment_status')
                        ->options(
                            collect(EmploymentStatus::cases())
                                ->mapWithKeys(fn (EmploymentStatus $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->nullable(),

                    DatePicker::make('start_date')
                        ->nullable()
                        ->native(false),

                    TextInput::make('contracted_hours_per_week')
                        ->label(__('hr.resources.employees.fields.contracted_hours_per_week'))
                        ->numeric()
                        ->nullable(),
                ]),

            Section::make(__('hr.resources.employees.sections.emergency_contact'))
                ->columns(3)
                ->schema([
                    TextInput::make('emergency_contact_name')
                        ->label(__('hr.resources.employees.fields.emergency_contact_name'))
                        ->nullable()
                        ->maxLength(255),

                    TextInput::make('emergency_contact_phone')
                        ->label(__('hr.resources.employees.fields.emergency_contact_phone'))
                        ->tel()
                        ->nullable()
                        ->maxLength(50),

                    TextInput::make('emergency_contact_relationship')
                        ->label(__('hr.resources.employees.fields.emergency_contact_relationship'))
                        ->nullable()
                        ->maxLength(100),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('hr.resources.employees.columns.name'))
                    ->getStateUsing(fn (Employee $record) => trim(implode(' ', array_filter([$record->first_name, $record->middle_name, $record->last_name]))))
                    ->searchable(['first_name', 'last_name'])
                    ->weight(FontWeight::Bold),

                TextColumn::make('email')
                    ->color('gray')
                    ->placeholder('—'),

                TextColumn::make('department.name')
                    ->label(__('hr.resources.employees.columns.department'))
                    ->badge()
                    ->placeholder('—'),

                TextColumn::make('employment_type')
                    ->label(__('hr.resources.employees.columns.type'))
                    ->badge()
                    ->formatStateUsing(fn (?EmploymentType $state) => $state?->label())
                    ->placeholder('—'),

                TextColumn::make('employment_status')
                    ->label(__('hr.resources.employees.columns.status'))
                    ->badge()
                    ->formatStateUsing(fn (?EmploymentStatus $state) => $state?->label())
                    ->color(fn (?EmploymentStatus $state) => $state?->color())
                    ->placeholder('—'),

                TextColumn::make('start_date')
                    ->label(__('hr.resources.employees.columns.start_date'))
                    ->date('d M Y')
                    ->placeholder('—'),
            ])
            ->striped()
            ->filters([
                SelectFilter::make('employment_status')
                    ->options(
                        collect(EmploymentStatus::cases())
                            ->mapWithKeys(fn (EmploymentStatus $case) => [$case->value => $case->label()])
                            ->toArray()
                    ),

                SelectFilter::make('employment_type')
                    ->options(
                        collect(EmploymentType::cases())
                            ->mapWithKeys(fn (EmploymentType $case) => [$case->value => $case->label()])
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['department']);
    }

    public static function getRelationManagers(): array
    {
        return [
            DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListEmployees::route('/'),
            'create' => CreateEmployee::route('/create'),
            'edit'   => EditEmployee::route('/{record}/edit'),
        ];
    }
}
