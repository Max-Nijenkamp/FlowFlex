<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Resources\EmployeeResource\Pages\CreateEmployee;
use App\Filament\Hr\Resources\EmployeeResource\Pages\EditEmployee;
use App\Filament\Hr\Resources\EmployeeResource\Pages\ListEmployees;
use App\Models\HR\Employee;
use App\Services\Core\BillingService;
use App\Support\Services\CompanyContext;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-user-group';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Employees';
    }

    public static function getNavigationLabel(): string
    {
        return 'Employees';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function canAccess(): bool
    {
        if (! auth()->check()) {
            return false;
        }
        $ctx = app(CompanyContext::class);
        if (! $ctx->hasCompany()) {
            return false;
        }

        return app(BillingService::class)
            ->enforceModuleAccess($ctx->current(), 'hr.profiles');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Personal Details')->schema([
                TextInput::make('first_name')
                    ->required()
                    ->maxLength(100),
                TextInput::make('last_name')
                    ->required()
                    ->maxLength(100),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(50),
                DatePicker::make('date_of_birth')
                    ->label('Date of birth'),
                TextInput::make('emergency_contact_name')
                    ->maxLength(100),
                TextInput::make('emergency_contact_phone')
                    ->tel()
                    ->maxLength(50),
            ])->columns(2),

            Section::make('Employment Details')->schema([
                TextInput::make('employee_number')
                    ->required()
                    ->maxLength(50),
                DatePicker::make('hire_date')
                    ->required(),
                Select::make('employment_type')
                    ->options([
                        'full_time'  => 'Full Time',
                        'part_time'  => 'Part Time',
                        'contractor' => 'Contractor',
                        'intern'     => 'Intern',
                    ])
                    ->required(),
                Select::make('status')
                    ->options([
                        'active'    => 'Active',
                        'inactive'  => 'Inactive',
                        'on_leave'  => 'On Leave',
                        'terminated' => 'Terminated',
                    ])
                    ->required()
                    ->default('active'),
                TextInput::make('department')
                    ->maxLength(100),
                TextInput::make('job_title')
                    ->maxLength(100),
                TextInput::make('location')
                    ->maxLength(100),
                DatePicker::make('termination_date'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_path')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn (Employee $record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->full_name) . '&background=random'),
                TextColumn::make('first_name')
                    ->label('Name')
                    ->formatStateUsing(fn (Employee $record) => $record->full_name)
                    ->sortable()
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('employee_number')
                    ->label('Emp #')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('job_title')
                    ->placeholder('—')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('department')
                    ->placeholder('—')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'     => 'success',
                        'terminated' => 'danger',
                        'on_leave'   => 'warning',
                        default      => 'gray',
                    }),
                TextColumn::make('hire_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active'     => 'Active',
                        'inactive'   => 'Inactive',
                        'on_leave'   => 'On Leave',
                        'terminated' => 'Terminated',
                    ]),
                SelectFilter::make('department')
                    ->options(fn () => Employee::withoutGlobalScopes()
                        ->where('company_id', app(CompanyContext::class)->currentId())
                        ->whereNotNull('department')
                        ->distinct()
                        ->pluck('department', 'department')
                        ->toArray()),
                SelectFilter::make('employment_type')
                    ->options([
                        'full_time'  => 'Full Time',
                        'part_time'  => 'Part Time',
                        'contractor' => 'Contractor',
                        'intern'     => 'Intern',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
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
