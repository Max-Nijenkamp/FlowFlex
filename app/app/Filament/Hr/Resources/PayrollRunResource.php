<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Resources\PayrollRunResource\Pages\CreatePayrollRun;
use App\Filament\Hr\Resources\PayrollRunResource\Pages\EditPayrollRun;
use App\Filament\Hr\Resources\PayrollRunResource\Pages\ListPayrollRuns;
use App\Models\HR\PayrollRun;
use App\Services\Core\BillingService;
use App\Support\Services\CompanyContext;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
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

class PayrollRunResource extends Resource
{
    protected static ?string $model = PayrollRun::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-banknotes';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Payroll';
    }

    public static function getNavigationLabel(): string
    {
        return 'Payroll Runs';
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
            ->enforceModuleAccess($ctx->current(), 'hr.payroll');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Payroll Run Details')->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('May 2026 Payroll'),
                Select::make('currency')
                    ->options([
                        'EUR' => 'EUR — Euro',
                        'USD' => 'USD — US Dollar',
                        'GBP' => 'GBP — British Pound',
                    ])
                    ->default('EUR')
                    ->required(),
                DatePicker::make('period_start')->required(),
                DatePicker::make('period_end')->required(),
                DatePicker::make('pay_date')->required(),
                Select::make('status')
                    ->options([
                        'draft'      => 'Draft',
                        'processing' => 'Processing',
                        'approved'   => 'Approved',
                        'paid'       => 'Paid',
                        'cancelled'  => 'Cancelled',
                    ])
                    ->default('draft'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('period_start')->date()->label('Period Start')->sortable(),
                TextColumn::make('period_end')->date()->label('Period End')->sortable(),
                TextColumn::make('pay_date')->date()->label('Pay Date')->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'paid'     => 'success',
                        'draft'    => 'gray',
                        'cancelled' => 'danger',
                        default    => 'warning',
                    }),
                TextColumn::make('total_gross')
                    ->label('Total Gross')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('entries_count')
                    ->label('Employees')
                    ->counts('entries'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft'      => 'Draft',
                        'processing' => 'Processing',
                        'approved'   => 'Approved',
                        'paid'       => 'Paid',
                        'cancelled'  => 'Cancelled',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (PayrollRun $record) => $record->isDraft())
                    ->requiresConfirmation()
                    ->action(function (PayrollRun $record): void {
                        $record->update([
                            'status'      => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()->title('Payroll run approved')->success()->send();
                    }),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPayrollRuns::route('/'),
            'create' => CreatePayrollRun::route('/create'),
            'edit'   => EditPayrollRun::route('/{record}/edit'),
        ];
    }
}
