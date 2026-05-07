<?php

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Enums\NavigationGroup;
use App\Filament\Hr\Resources\PayslipResource\Pages\ListPayslips;
use App\Models\Hr\Payslip;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayslipResource extends Resource
{
    protected static ?string $model = Payslip::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Payroll->label();
    }

    public static function getModelLabel(): string
    {
        return __('hr.resources.payslips.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('hr.resources.payslips.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr.payroll.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('hr.payroll.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label(__('hr.resources.payslips.columns.employee'))
                    ->searchable(),

                TextColumn::make('payRun.reference')
                    ->label(__('hr.resources.payslips.columns.pay_run'))
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('period_start')
                    ->label(__('hr.resources.payslips.columns.period_start'))
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('period_end')
                    ->label(__('hr.resources.payslips.columns.period_end'))
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('generated_at')
                    ->label(__('hr.resources.payslips.columns.generated'))
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('sent_at')
                    ->label(__('hr.resources.payslips.columns.sent'))
                    ->dateTime('d M Y H:i')
                    ->placeholder('Not sent')
                    ->sortable(),
            ])
            ->defaultSort('generated_at', 'desc')
            ->striped()
            ->paginated([25, 50]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['employee', 'payRun']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayslips::route('/'),
        ];
    }
}
