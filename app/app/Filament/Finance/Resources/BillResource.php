<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Contracts\BillingServiceInterface;
use App\Contracts\Finance\ApServiceInterface;
use App\Models\Finance\Account;
use App\Models\Finance\Bill;
use App\Models\Finance\Supplier;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
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

    public static function canCreate(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.ap.create');
    }

    /**
     * Total is derived from lines by ApService::createBill — no free-form
     * amount field; currency stays at the EUR column default.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Bill')
                ->columns(2)
                ->components([
                    Select::make('supplier_id')->label('Supplier')
                        ->options(fn () => Supplier::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    TextInput::make('bill_number')->required()->maxLength(60),
                    DatePicker::make('bill_date')->required()->default(now()),
                    DatePicker::make('due_date')->required()->afterOrEqual('bill_date'),
                    TextInput::make('early_discount_percent')->label('Early discount (%)')
                        ->numeric()->minValue(0)->maxValue(100)->nullable(),
                    DatePicker::make('early_discount_until')->label('Early discount until')
                        ->nullable(),
                ]),
            Section::make('Lines')
                ->columns(1)
                ->components([
                    Repeater::make('lines')
                        ->hiddenLabel()
                        ->schema([
                            TextInput::make('description')->required(),
                            Select::make('account_code')->label('GL account')
                                ->options(fn () => Account::query()->orderBy('code')->get()
                                    ->mapWithKeys(fn (Account $a): array => [$a->code => "{$a->code} — {$a->name}"])
                                    ->all())
                                ->searchable()
                                ->required(),
                            TextInput::make('amount_cents')->label('Amount (cents)')
                                ->numeric()->integer()->minValue(1)->required(),
                        ])
                        ->columns(3)
                        ->minItems(1)
                        ->defaultItems(1),
                ]),
        ]);
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
