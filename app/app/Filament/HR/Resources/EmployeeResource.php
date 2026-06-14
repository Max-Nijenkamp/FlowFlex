<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Contracts\HR\EmployeeServiceInterface;
use App\Data\HR\OffboardEmployeeData;
use App\Models\HR\Department;
use App\Models\HR\Employee;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Employees';

    protected static ?string $recordTitleAttribute = 'last_name';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.employees.view-any')
            && app(BillingServiceInterface::class)->hasModule('hr.profiles');
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var Employee $record */
        return $record->full_name;
    }

    /** @return list<string> */
    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'email'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('first_name')->required()->maxLength(100),
            TextInput::make('last_name')->required()->maxLength(100),
            TextInput::make('email')->email()->required()->label('Work email'),
            TextInput::make('phone')->tel(),
            DatePicker::make('hire_date')->required(),
            TextInput::make('job_title')->required()->maxLength(150),
            Select::make('department_id')->label('Department')
                ->options(fn () => Department::query()->pluck('name', 'id'))->nullable(),
            Select::make('manager_id')->label('Manager')
                ->options(fn () => Employee::query()->get()->pluck('full_name', 'id'))->nullable(),
            Select::make('employment_type')
                ->options(['full-time' => 'Full-time', 'part-time' => 'Part-time', 'contractor' => 'Contractor'])
                ->required(),
            // Sensitive fields gated by hr.employees.view-sensitive
            TextInput::make('personal_email')->email()
                ->visible(fn () => Auth::guard('web')->user()->can('hr.employees.view-sensitive')),
            DatePicker::make('date_of_birth')->before('today')
                ->visible(fn () => Auth::guard('web')->user()->can('hr.employees.view-sensitive')),
            TextInput::make('national_id')->maxLength(50)
                ->visible(fn () => Auth::guard('web')->user()->can('hr.employees.view-sensitive')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->columns([
                TextColumn::make('employee_number')->label('#')->sortable(),
                TextColumn::make('full_name')->label('Name')
                    ->state(fn (Employee $r) => $r->full_name)
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('job_title')->searchable(),
                TextColumn::make('department.name')->label('Department')->placeholder('—'),
                TextColumn::make('status')->badge()
                    ->color(fn ($state): string => match ((string) $state) {
                        'active' => 'success',
                        'terminated' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('hire_date')->date()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'active' => 'Active', 'on_leave' => 'On leave', 'suspended' => 'Suspended', 'terminated' => 'Terminated',
                ]),
                SelectFilter::make('department_id')->label('Department')
                    ->options(fn () => Department::query()->pluck('name', 'id')),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('offboard')
                    ->icon(Heroicon::OutlinedArrowRightStartOnRectangle)
                    ->color('danger')
                    ->visible(fn (Employee $r) => (string) $r->status !== 'terminated'
                        && Auth::guard('web')->user()->can('hr.employees.offboard'))
                    ->schema([
                        DatePicker::make('termination_date')->required(),
                        Textarea::make('termination_reason')->required()->maxLength(1000),
                    ])
                    ->action(function (Employee $record, array $data): void {
                        app(EmployeeServiceInterface::class)->offboard(new OffboardEmployeeData(
                            employee_id: $record->id,
                            termination_date: $data['termination_date'],
                            termination_reason: $data['termination_reason'],
                        ));
                        Notification::make()->success()->title('Employee offboarded')->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => EmployeeResource\Pages\ListEmployees::route('/'),
            'create' => EmployeeResource\Pages\CreateEmployee::route('/create'),
            'edit' => EmployeeResource\Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
