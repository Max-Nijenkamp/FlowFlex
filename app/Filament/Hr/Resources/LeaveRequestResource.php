<?php

namespace App\Filament\Hr\Resources;

use App\Enums\Hr\LeaveRequestStatus;
use App\Events\Hr\LeaveApproved;
use App\Events\Hr\LeaveRejected;
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
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Leave->label();
    }

    public static function getModelLabel(): string
    {
        return __('hr.resources.leave_requests.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('hr.resources.leave_requests.plural');
    }

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
            Section::make(__('hr.resources.leave_requests.sections.details'))
                ->schema([
                    Select::make('employee_id')
                        ->label(__('hr.resources.leave_requests.fields.employee'))
                        ->relationship('employee', 'first_name')
                        ->getOptionLabelFromRecordUsing(fn (Employee $record) => trim("{$record->first_name} {$record->last_name}"))
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('leave_type_id')
                        ->label(__('hr.resources.leave_requests.fields.leave_type'))
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
                        ->label(__('hr.resources.leave_requests.fields.half_day'))
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
                    ->label(__('hr.resources.leave_requests.columns.employee'))
                    ->getStateUsing(fn (LeaveRequest $record) => trim("{$record->employee?->first_name} {$record->employee?->last_name}")),

                TextColumn::make('leaveType.name')
                    ->label(__('hr.resources.leave_requests.columns.leave_type')),

                TextColumn::make('start_date')
                    ->label(__('hr.resources.leave_requests.columns.start'))
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label(__('hr.resources.leave_requests.columns.end'))
                    ->date('d M Y'),

                TextColumn::make('total_days')
                    ->label(__('hr.resources.leave_requests.columns.days'))
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
                Action::make('approve')
                    ->label(__('hr.resources.leave_requests.actions.approve'))
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (LeaveRequest $record) => $record->status === LeaveRequestStatus::Pending)
                    ->requiresConfirmation()
                    ->action(function (LeaveRequest $record): void {
                        $record->update(['status' => LeaveRequestStatus::Approved]);
                        event(new LeaveApproved($record));
                    }),

                Action::make('reject')
                    ->label(__('hr.resources.leave_requests.actions.reject'))
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (LeaveRequest $record) => $record->status === LeaveRequestStatus::Pending)
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label(__('hr.resources.leave_requests.fields.rejection_reason'))
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (LeaveRequest $record, array $data): void {
                        $record->update([
                            'status'           => LeaveRequestStatus::Rejected,
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                        event(new LeaveRejected($record));
                    }),

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
