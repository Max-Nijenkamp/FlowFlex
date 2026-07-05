<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources;

use App\Contracts\Hr\EmployeeServiceInterface;
use App\Data\Hr\OffboardEmployeeData;
use App\Models\Hr\Department;
use App\Models\Hr\Employee;
use App\Models\User;
use App\Services\BillingService;
use App\Services\Hr\EmployeeService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Throwable;

/**
 * Employee roster (hr.profiles). Sensitive fields (national ID, DOB,
 * personal email) render only with hr.employees.view-sensitive; the
 * offboard action is the only path to terminated.
 */
class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Employees';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'employees';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('hr.employees.view-any')
            && app(BillingService::class)->hasModule('hr.profiles');
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->can('hr.employees.create');
    }

    public static function canSeeSensitive(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->can('hr.employees.view-sensitive');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Person')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('first_name')->required()->maxLength(120),
                    TextInput::make('last_name')->required()->maxLength(120),
                    TextInput::make('email')->label('Work email')->email()->required(),
                    TextInput::make('phone')->tel()->helperText('Stored as E.164.'),
                    TextInput::make('personal_email')
                        ->email()
                        ->visible(fn (): bool => self::canSeeSensitive())
                        ->helperText('Encrypted at rest.'),
                    DatePicker::make('date_of_birth')
                        ->native(false)
                        ->maxDate(now()->subYears(15))
                        ->visible(fn (): bool => self::canSeeSensitive())
                        ->helperText('Encrypted at rest.'),
                    TextInput::make('national_id')
                        ->visible(fn (): bool => self::canSeeSensitive())
                        ->helperText('Encrypted at rest.'),
                ]),
            Section::make('Employment')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('job_title')->required()->maxLength(160),
                    Select::make('employment_type')
                        ->options([
                            'full-time' => 'Full-time', 'part-time' => 'Part-time', 'contractor' => 'Contractor',
                        ])
                        ->default('full-time')
                        ->required(),
                    DatePicker::make('hire_date')->native(false)->default(now())->required(),
                    Select::make('department_id')
                        ->label('Department')
                        ->options(fn (): array => Department::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->placeholder('None'),
                    Select::make('manager_id')
                        ->label('Manager')
                        ->options(fn (?Employee $record): array => Employee::query()
                            ->when($record, fn ($query) => $query->whereKeyNot($record->id))
                            ->where('status', '!=', 'terminated')
                            ->get()
                            ->sortBy('last_name')
                            ->mapWithKeys(fn (Employee $employee): array => [$employee->id => $employee->full_name])
                            ->all())
                        ->searchable()
                        ->placeholder('None'),
                    Select::make('user_id')
                        ->label('Portal login')
                        ->options(fn (): array => User::query()->get()->mapWithKeys(
                            fn (User $user): array => [$user->id => $user->full_name.' ('.$user->email.')'],
                        )->all())
                        ->searchable()
                        ->placeholder('No login'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_number')->label('#')->sortable(),
                TextColumn::make('full_name')
                    ->label('Name')
                    ->state(fn (Employee $record): string => $record->full_name)
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('job_title')->searchable(),
                TextColumn::make('department.name')->label('Department')->placeholder('—'),
                TextColumn::make('manager.full_name')
                    ->label('Manager')
                    ->state(fn (Employee $record): string => $record->manager()->first()->full_name ?? '—'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (Employee $record): string => str((string) $record->status)->replace('_', ' ')->headline()->toString())
                    ->color(fn (Employee $record): string => match ((string) $record->status) {
                        'active' => 'success', 'on_leave' => 'info', 'suspended' => 'warning', default => 'gray',
                    }),
                TextColumn::make('hire_date')->date('d M Y')->sortable(),
            ])
            ->defaultSort('employee_number')
            ->filters([
                SelectFilter::make('status')->options([
                    'active' => 'Active', 'on_leave' => 'On leave', 'suspended' => 'Suspended', 'terminated' => 'Terminated',
                ]),
                SelectFilter::make('department_id')
                    ->label('Department')
                    ->options(fn (): array => Department::query()->orderBy('name')->pluck('name', 'id')->all()),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->visible(function (Employee $record): bool {
                            $user = Auth::user();

                            return (string) $record->status !== 'terminated'
                                && $user instanceof User
                                && $user->can('hr.employees.update');
                        })
                        ->mutateDataUsing(function (array $data, Employee $record): array {
                            if (($data['manager_id'] ?? null) !== null) {
                                EmployeeService::assertNoCycle($record->id, $data['manager_id']);
                            }
                            $data['phone'] = EmployeeService::normalisePhone($data['phone'] ?? null);

                            return $data;
                        }),
                    Action::make('offboard')
                        ->icon('heroicon-o-arrow-right-start-on-rectangle')
                        ->color('danger')
                        ->visible(function (Employee $record): bool {
                            $user = Auth::user();

                            return (string) $record->status !== 'terminated'
                                && $user instanceof User
                                && $user->can('hr.employees.offboard');
                        })
                        ->schema([
                            DatePicker::make('termination_date')->native(false)->default(now())->required(),
                            Textarea::make('reason')->required()->rows(2),
                        ])
                        ->requiresConfirmation()
                        ->modalDescription('Terminates the employment, fires the offboarding signals, and locks the record.')
                        ->action(function (Employee $record, array $data): void {
                            try {
                                app(EmployeeServiceInterface::class)->offboard(new OffboardEmployeeData(
                                    employeeId: $record->id,
                                    terminationDate: $data['termination_date'],
                                    reason: $data['reason'],
                                ));
                                Notification::make()->success()->title('Employee offboarded')->send();
                            } catch (Throwable $e) {
                                Notification::make()->danger()->title($e->getMessage())->send();
                            }
                        }),
                ]),
            ])
            ->emptyStateHeading('No employees yet')
            ->emptyStateDescription('Hire your first employee — every other HR module hangs off this roster.');
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
