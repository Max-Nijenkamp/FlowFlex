<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources;

use App\Models\Hr\Department;
use App\Models\Hr\Employee;
use App\Models\User;
use App\Services\BillingService;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/** Departments (hr.profiles). */
class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static string|\UnitEnum|null $navigationGroup = 'Employees';

    protected static ?int $navigationSort = 2;

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

        return $user instanceof User && $user->can('hr.employees.update');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Department')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->maxLength(120),
                    Select::make('head_employee_id')
                        ->label('Department head')
                        ->options(fn (): array => Employee::query()
                            ->where('status', '!=', 'terminated')
                            ->get()
                            ->sortBy('last_name')
                            ->mapWithKeys(fn (Employee $employee): array => [$employee->id => $employee->full_name])
                            ->all())
                        ->searchable()
                        ->placeholder('None'),
                    Select::make('parent_department_id')
                        ->label('Parent department')
                        ->options(fn (?Department $record): array => Department::query()
                            ->when($record, fn ($query) => $query->whereKeyNot($record->id))
                            ->orderBy('name')->pluck('name', 'id')->all())
                        ->placeholder('Top level'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('head.full_name')
                    ->label('Head')
                    ->state(fn (Department $record): string => $record->head()->first()->full_name ?? '—'),
                TextColumn::make('employees_count')->label('Employees')->counts('employees'),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, Department $record): void {
                        if ($record->employees()->exists()) {
                            Notification::make()->danger()
                                ->title('This department still has employees — reassign them first.')
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->emptyStateHeading('No departments yet');
    }

    public static function getPages(): array
    {
        return [
            'index' => DepartmentResource\Pages\ListDepartments::route('/'),
            'create' => DepartmentResource\Pages\CreateDepartment::route('/create'),
            'edit' => DepartmentResource\Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
