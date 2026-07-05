<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Models\Finance\Customer;
use App\Models\User;
use App\Services\BillingService;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/** Invoice recipients (finance.invoicing). */
class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('finance.invoices.view-any')
            && app(BillingService::class)->hasModule('finance.invoicing');
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->can('finance.invoices.manage-customers');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Customer')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->maxLength(160),
                    TextInput::make('email')->email()->required(),
                    TextInput::make('vat_number')->label('VAT number')->maxLength(32),
                    TextInput::make('payment_terms_days')
                        ->label('Payment terms (days)')
                        ->numeric()->minValue(0)->maxValue(120)
                        ->default(14)
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('vat_number')->label('VAT')->placeholder('—'),
                TextColumn::make('payment_terms_days')
                    ->label('Terms')
                    ->formatStateUsing(fn (Customer $record): string => $record->payment_terms_days.' days'),
                TextColumn::make('invoices_count')->label('Invoices')->counts('invoices'),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make()
                    ->visible(function (): bool {
                        $user = Auth::user();

                        return $user instanceof User && $user->can('finance.invoices.manage-customers');
                    }),
                DeleteAction::make()
                    ->visible(function (): bool {
                        $user = Auth::user();

                        return $user instanceof User && $user->can('finance.invoices.manage-customers');
                    })
                    ->before(function (DeleteAction $action, Customer $record): void {
                        if ($record->invoices()->exists()) {
                            Notification::make()->danger()
                                ->title('This customer has invoices — they cannot be removed.')
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->emptyStateHeading('No customers yet')
            ->emptyStateDescription('Customers appear here manually or automatically from won CRM deals.');
    }

    public static function getPages(): array
    {
        return [
            'index' => CustomerResource\Pages\ListCustomers::route('/'),
            'create' => CustomerResource\Pages\CreateCustomer::route('/create'),
            'edit' => CustomerResource\Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
