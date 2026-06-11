<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Contracts\BillingServiceInterface;
use App\Contracts\Finance\ApServiceInterface;
use App\Models\Finance\Bill;
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

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Payables';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.ap.view-any')
            && app(BillingServiceInterface::class)->hasModule('finance.ap');
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
                TextColumn::make('bill_number')->searchable(),
                TextColumn::make('supplier.name')->label('Supplier'),
                TextColumn::make('amount_cents')->label('Amount')->formatStateUsing(fn (int $state) => '€'.number_format($state / 100, 2)),
                TextColumn::make('due_date')->date()->sortable(),
                TextColumn::make('status')->badge(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->icon(Heroicon::OutlinedCheck)->color('success')
                    ->visible(fn (Bill $r) => (string) $r->status === 'draft'
                        && Auth::guard('web')->user()->can('finance.ap.approve'))
                    ->requiresConfirmation()
                    ->action(function (Bill $record): void {
                        app(ApServiceInterface::class)->approveBill($record->id);
                        Notification::make()->success()->title('Bill approved — liability posted')->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => BillResource\Pages\ListBills::route('/'),
        ];
    }
}
