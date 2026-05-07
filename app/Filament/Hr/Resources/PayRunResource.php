<?php

namespace App\Filament\Hr\Resources;

use App\Enums\Hr\PayFrequency;
use App\Enums\Hr\PayRunStatus;
use App\Filament\Hr\Enums\NavigationGroup;
use App\Filament\Hr\Resources\PayRunResource\Pages\CreatePayRun;
use App\Filament\Hr\Resources\PayRunResource\Pages\EditPayRun;
use App\Filament\Hr\Resources\PayRunResource\Pages\ListPayRuns;
use App\Models\Hr\PayRun;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PayRunResource extends Resource
{
    protected static ?string $model = PayRun::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-currency-pound';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Payroll->label();
    }

    public static function getModelLabel(): string
    {
        return __('hr.resources.pay_runs.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('hr.resources.pay_runs.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr.pay-runs.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr.pay-runs.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('hr.pay-runs.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('hr.pay-runs.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('hr.resources.pay_runs.sections.details'))
                ->schema([
                    Select::make('payroll_entity_id')
                        ->label(__('hr.resources.pay_runs.fields.payroll_entity'))
                        // payroll_entity_id is NOT NULL in the migration — required
                        ->options(fn () => \App\Models\Hr\PayrollEntity::query()->pluck('name', 'id')->toArray())
                        ->required()
                        ->searchable(),

                    Select::make('pay_frequency')
                        ->options(
                            collect(PayFrequency::cases())
                                ->mapWithKeys(fn (PayFrequency $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->required(),

                    DatePicker::make('pay_period_start')
                        ->label(__('hr.resources.pay_runs.fields.period_start'))
                        ->required()
                        ->native(false),

                    DatePicker::make('pay_period_end')
                        ->label(__('hr.resources.pay_runs.fields.period_end'))
                        ->required()
                        ->native(false),

                    DatePicker::make('payment_date')
                        ->label(__('hr.resources.pay_runs.fields.payment_date'))
                        ->required()
                        ->native(false),

                    Select::make('status')
                        ->options(
                            collect(PayRunStatus::cases())
                                ->mapWithKeys(fn (PayRunStatus $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->default(PayRunStatus::Draft->value)
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pay_period_start')
                    ->label(__('hr.resources.pay_runs.columns.period_start'))
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('pay_period_end')
                    ->label(__('hr.resources.pay_runs.columns.period_end'))
                    ->date('d M Y'),

                TextColumn::make('payment_date')
                    ->label(__('hr.resources.pay_runs.columns.payment_date'))
                    ->date('d M Y'),

                TextColumn::make('pay_frequency')
                    ->label(__('hr.resources.pay_runs.columns.frequency'))
                    ->badge()
                    ->formatStateUsing(fn (?PayFrequency $state) => $state?->label()),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?PayRunStatus $state) => $state?->label())
                    ->color(fn (?PayRunStatus $state) => $state?->color()),

                TextColumn::make('total_gross')
                    ->label(__('hr.resources.pay_runs.columns.gross'))
                    ->money(fn ($record) => $record?->payrollEntity?->currency ?? 'EUR')
                    ->placeholder('—'),

                TextColumn::make('total_net')
                    ->label(__('hr.resources.pay_runs.columns.net'))
                    ->money(fn ($record) => $record?->payrollEntity?->currency ?? 'EUR')
                    ->placeholder('—'),
            ])
            ->defaultSort('pay_period_start', 'desc')
            ->striped()
            ->filters([
                SelectFilter::make('status')
                    ->options(
                        collect(PayRunStatus::cases())
                            ->mapWithKeys(fn (PayRunStatus $case) => [$case->value => $case->label()])
                            ->toArray()
                    ),

                SelectFilter::make('pay_frequency')
                    ->label(__('hr.resources.pay_runs.filters.frequency'))
                    ->options(
                        collect(PayFrequency::cases())
                            ->mapWithKeys(fn (PayFrequency $case) => [$case->value => $case->label()])
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
        return parent::getEloquentQuery()->with(['payrollEntity']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPayRuns::route('/'),
            'create' => CreatePayRun::route('/create'),
            'edit'   => EditPayRun::route('/{record}/edit'),
        ];
    }
}
