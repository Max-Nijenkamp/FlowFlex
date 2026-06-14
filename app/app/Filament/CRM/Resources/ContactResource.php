<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\CRM\Account;
use App\Models\CRM\Contact;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static string|UnitEnum|null $navigationGroup = 'Contacts';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('crm.contacts.view-any')
            && app(BillingServiceInterface::class)->hasModule('crm.contacts');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Person')
                ->columns(2)
                ->components([
                    TextInput::make('first_name')->required()->maxLength(100),
                    TextInput::make('last_name')->required()->maxLength(100),
                    TextInput::make('email')->email(),
                    TextInput::make('phone')->tel(),
                    TextInput::make('job_title')->maxLength(150),
                    Select::make('account_id')->label('Organisation')
                        ->options(fn () => Account::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),
                ]),
            Section::make('Lifecycle')
                ->columns(2)
                ->components([
                    Select::make('lifecycle_stage')
                        ->options([
                            'lead' => 'Lead', 'mql' => 'MQL', 'sql' => 'SQL',
                            'opportunity' => 'Opportunity', 'customer' => 'Customer', 'evangelist' => 'Evangelist',
                        ])
                        ->default('lead')
                        ->required(),
                    Select::make('source')
                        ->options(['website' => 'Website', 'referral' => 'Referral', 'linkedin' => 'LinkedIn', 'manual' => 'Manual'])
                        ->default('manual'),
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
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->columns([
                TextColumn::make('full_name')->label('Name')
                    ->state(fn (Contact $r) => $r->full_name)
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('email')->searchable(),
                TextColumn::make('account.name')->label('Account')->placeholder('—'),
                TextColumn::make('lifecycle_stage')->badge(),
                TextColumn::make('source')->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('lifecycle_stage')->options([
                    'lead' => 'Lead', 'mql' => 'MQL', 'sql' => 'SQL',
                    'opportunity' => 'Opportunity', 'customer' => 'Customer', 'evangelist' => 'Evangelist',
                ]),
                SelectFilter::make('account_id')->label('Organisation')
                    ->options(fn () => Account::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ContactResource\Pages\ListContacts::route('/'),
            'create' => ContactResource\Pages\CreateContact::route('/create'),
            'edit' => ContactResource\Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
