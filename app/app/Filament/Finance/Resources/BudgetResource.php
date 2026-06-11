<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Contracts\BillingServiceInterface;
use App\Contracts\Finance\BudgetServiceInterface;
use App\Models\Finance\Budget;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static string|UnitEnum|null $navigationGroup = 'Planning';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.budgets.view-any')
            && app(BillingServiceInterface::class)->hasModule('finance.budgets');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('fiscal_year'),
                TextColumn::make('version')->badge(),
                TextColumn::make('status')->badge(),
                TextColumn::make('lines_count')->counts('lines')->label('Lines'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->icon(Heroicon::OutlinedCheck)->color('success')
                    ->visible(fn (Budget $r) => $r->status === 'draft'
                        && Auth::guard('web')->user()->can('finance.budgets.approve'))
                    ->requiresConfirmation()
                    ->action(function (Budget $record): void {
                        app(BudgetServiceInterface::class)->approve($record->id);
                        Notification::make()->success()->title('Budget approved')->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => BudgetResource\Pages\ListBudgets::route('/'),
        ];
    }
}
