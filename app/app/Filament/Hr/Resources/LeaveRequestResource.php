<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Resources\LeaveRequestResource\Pages\CreateLeaveRequest;
use App\Filament\Hr\Resources\LeaveRequestResource\Pages\EditLeaveRequest;
use App\Filament\Hr\Resources\LeaveRequestResource\Pages\ListLeaveRequests;
use App\Models\HR\Employee;
use App\Models\HR\LeavePolicy;
use App\Models\HR\LeaveRequest;
use App\Services\Core\BillingService;
use App\Support\Services\CompanyContext;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-calendar-days';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Leave';
    }

    public static function getNavigationLabel(): string
    {
        return 'Leave Requests';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
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
            ->enforceModuleAccess($ctx->current(), 'hr.leave');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Leave Request Details')->schema([
                Select::make('employee_id')
                    ->label('Employee')
                    ->options(fn () => Employee::withoutGlobalScopes()
                        ->where('company_id', app(CompanyContext::class)->currentId())
                        ->where('status', 'active')
                        ->get()
                        ->mapWithKeys(fn ($e) => [$e->id => $e->full_name])
                        ->toArray())
                    ->searchable()
                    ->required(),
                Select::make('policy_id')
                    ->label('Leave Policy')
                    ->options(fn () => LeavePolicy::withoutGlobalScopes()
                        ->where('company_id', app(CompanyContext::class)->currentId())
                        ->where('is_active', true)
                        ->pluck('name', 'id')
                        ->toArray())
                    ->required(),
                DatePicker::make('start_date')->required(),
                DatePicker::make('end_date')->required(),
                TextInput::make('days_requested')
                    ->numeric()
                    ->required()
                    ->minValue(0.5),
                Textarea::make('reason')->rows(3),
                Select::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'approved'  => 'Approved',
                        'rejected'  => 'Rejected',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('pending'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.first_name')
                    ->label('Employee')
                    ->formatStateUsing(fn (LeaveRequest $record) => $record->employee?->full_name ?? '—')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('policy.name')
                    ->label('Leave Type')
                    ->sortable(),
                TextColumn::make('start_date')->date()->sortable(),
                TextColumn::make('end_date')->date()->sortable(),
                TextColumn::make('days_requested')->label('Days'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved'  => 'success',
                        'rejected'  => 'danger',
                        'cancelled' => 'gray',
                        default     => 'warning',
                    }),
                TextColumn::make('approver.first_name')
                    ->label('Approved By')
                    ->formatStateUsing(fn (LeaveRequest $record) => $record->approver
                        ? "{$record->approver->first_name} {$record->approver->last_name}"
                        : '—'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'approved'  => 'Approved',
                        'rejected'  => 'Rejected',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (LeaveRequest $record) => $record->isPending())
                    ->requiresConfirmation()
                    ->action(function (LeaveRequest $record): void {
                        $record->update([
                            'status'      => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()->title('Leave approved')->success()->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (LeaveRequest $record) => $record->isPending())
                    ->requiresConfirmation()
                    ->action(function (LeaveRequest $record): void {
                        $record->update(['status' => 'rejected']);
                        Notification::make()->title('Leave rejected')->warning()->send();
                    }),
                EditAction::make(),
            ]);
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
