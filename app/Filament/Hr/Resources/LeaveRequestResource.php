<?php

namespace App\Filament\Hr\Resources;

use App\Enums\Hr\LeaveRequestStatus;
use App\Filament\Hr\Enums\NavigationGroup;
use App\Filament\Hr\Resources\LeaveRequestResource\Pages\CreateLeaveRequest;
use App\Filament\Hr\Resources\LeaveRequestResource\Pages\EditLeaveRequest;
use App\Filament\Hr\Resources\LeaveRequestResource\Pages\ListLeaveRequests;
use App\Models\Hr\Employee;
use App\Models\Hr\LeaveRequest;
use App\Models\Hr\LeaveType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-paper-airplane';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::Leave;

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr.leave-requests.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr.leave-requests.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('hr.leave-requests.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('hr.leave-requests.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Leave Request Details')
                ->schema([
                    Select::make('employee_id')
                        ->label('Employee')
                        ->options(fn () => Employee::query()->get()->mapWithKeys(fn (Employee $e) => [$e->id => trim("{$e->first_name} {$e->last_name}")])->toArray())
                        ->required()
                        ->searchable(),

                    Select::make('leave_type_id')
                        ->label('Leave Type')
                        ->options(fn () => LeaveType::query()->where('is_active', true)->pluck('name', 'id')->toArray())
                        ->required()
                        ->searchable(),

                    DatePicker::make('start_date')
                        ->required()
                        ->native(false),

                    DatePicker::make('end_date')
                        ->required()
                        ->native(false),

                    Toggle::make('is_half_day')
                        ->label('Half Day')
                        ->default(false),

                    Textarea::make('reason')
                        ->nullable()
                        ->rows(3),

                    Select::make('status')
                        ->options(
                            collect(LeaveRequestStatus::cases())
                                ->mapWithKeys(fn (LeaveRequestStatus $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->default(LeaveRequestStatus::Pending->value)
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_name')
                    ->label('Employee')
                    ->getStateUsing(fn (LeaveRequest $record) => trim("{$record->employee->first_name} {$record->employee->last_name}")),

                TextColumn::make('leaveType.name')
                    ->label('Leave Type'),

                TextColumn::make('start_date')
                    ->label('Start')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('End')
                    ->date('d M Y'),

                TextColumn::make('total_days')
                    ->label('Days')
                    ->numeric(),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?LeaveRequestStatus $state) => $state?->label())
                    ->color(fn (?LeaveRequestStatus $state) => $state?->color()),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->filters([
                SelectFilter::make('status')
                    ->options(
                        collect(LeaveRequestStatus::cases())
                            ->mapWithKeys(fn (LeaveRequestStatus $case) => [$case->value => $case->label()])
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
        return parent::getEloquentQuery()->with(['employee', 'leaveType']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListLeaveRequests::route('/'),
            'create' => CreateLeaveRequest::route('/create'),
            'edit'   => EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
