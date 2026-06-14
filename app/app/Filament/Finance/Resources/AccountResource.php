<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\Finance\Account;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Ledger';

    protected static ?string $modelLabel = 'GL account';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.ledger.view-any')
            && app(BillingServiceInterface::class)->hasModule('finance.ledger');
    }

    public static function canCreate(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.ledger.post');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('GL account')
                ->columns(2)
                ->components([
                    TextInput::make('code')->required()->maxLength(20),
                    TextInput::make('name')->required()->maxLength(120),
                    Select::make('type')
                        ->options([
                            'asset' => 'Asset',
                            'liability' => 'Liability',
                            'equity' => 'Equity',
                            'revenue' => 'Revenue',
                            'expense' => 'Expense',
                        ])
                        ->required(),
                    Select::make('parent_account_id')->label('Parent account')
                        ->options(fn () => Account::query()->orderBy('code')->get()
                            ->mapWithKeys(fn (Account $a): array => [$a->id => "{$a->code} — {$a->name}"])
                            ->all())
                        ->searchable()
                        ->nullable(),
                    Toggle::make('is_active')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->orderBy('code'))
            ->columns([
                TextColumn::make('code')->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('type')->badge(),
                IconColumn::make('is_active')->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => AccountResource\Pages\ListAccounts::route('/'),
        ];
    }
}
