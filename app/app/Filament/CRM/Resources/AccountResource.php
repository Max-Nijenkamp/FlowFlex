<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\CRM\Account;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/** Organisations (CRM accounts) — companies your contacts and deals belong to. */
class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static string|UnitEnum|null $navigationGroup = 'Contacts';

    protected static ?string $modelLabel = 'organisation';

    protected static ?string $navigationLabel = 'Organisations';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('crm.contacts.view-any')
            && app(BillingServiceInterface::class)->hasModule('crm.contacts');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Organisation')
                ->columns(2)
                ->components([
                    TextInput::make('name')->required()->maxLength(150),
                    TextInput::make('industry')->maxLength(100),
                    TextInput::make('website')->url()->maxLength(255),
                    TextInput::make('phone')->tel(),
                    TextInput::make('employee_count')->numeric()->minValue(0),
                ]),
            Section::make('Attachments')
                ->components([
                    SpatieMediaLibraryFileUpload::make('attachments')
                        ->collection('attachments')
                        ->multiple()
                        ->downloadable()
                        ->maxSize(10_240)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->modifyQueryUsing(fn ($query) => $query->withCount(['contacts', 'deals'])->orderBy('name'))
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('industry')->placeholder('—')->toggleable(),
                TextColumn::make('contacts_count')->label('Contacts'),
                TextColumn::make('deals_count')->label('Deals'),
                TextColumn::make('lifetime_value_cents')->label('Lifetime value')
                    ->money('EUR', divideBy: 100)
                    ->sortable(),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => AccountResource\Pages\ListAccounts::route('/'),
            'create' => AccountResource\Pages\CreateAccount::route('/create'),
            'edit' => AccountResource\Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
