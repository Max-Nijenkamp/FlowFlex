<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\CRM\Account;
use App\Models\CRM\Contract;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Sales';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('crm.contracts.view-any')
            && app(BillingServiceInterface::class)->hasModule('crm.contracts');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contract')
                ->columns(2)
                ->components([
                    Select::make('account_id')->label('Organisation')
                        ->options(fn () => Account::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    TextInput::make('title')->required()->maxLength(160),
                    TextInput::make('value_cents')->label('Value (€)')->numeric()->required()
                        ->formatStateUsing(fn ($state): float => $state === null ? 0.0 : round($state / 100, 2))
                        ->dehydrateStateUsing(fn ($state): int => (int) round((float) $state * 100)),
                    Select::make('billing_interval')->label('Billing')
                        ->options(['one-off' => 'One-off', 'monthly' => 'Monthly', 'yearly' => 'Yearly'])
                        ->default('one-off'),
                    DatePicker::make('start_date')->required(),
                    DatePicker::make('end_date')->required(),
                    Toggle::make('auto_renew')->label('Auto-renew')->inline(false),
                    TextInput::make('notice_period_days')->label('Notice period (days)')->numeric(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('value_cents')->label('Value')->formatStateUsing(fn (int $state) => '€'.number_format($state / 100, 2)),
                TextColumn::make('billing_interval')->badge(),
                TextColumn::make('end_date')->date()->sortable(),
                TextColumn::make('status')->badge(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ContractResource\Pages\ListContracts::route('/'),
        ];
    }
}
