<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CompanyResource\Pages\CreateCompany;
use App\Filament\Admin\Resources\CompanyResource\Pages\EditCompany;
use App\Filament\Admin\Resources\CompanyResource\Pages\ListCompanies;
use App\Filament\Admin\Resources\CompanyResource\Pages\ViewCompany;
use App\Filament\Admin\Resources\CompanyResource\RelationManagers\UsersRelationManager;
use App\Models\Company;
use App\Services\Foundation\CompanyService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-building-office';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Companies';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Company Details')->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(100)
                    ->unique(Company::class, 'slug', ignoreRecord: true)
                    ->helperText('URL-safe identifier, e.g. acme-corp'),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->label('Primary email'),
                TextInput::make('country')
                    ->maxLength(100)
                    ->label('Country'),
                Select::make('timezone')
                    ->options(fn () => array_combine(
                        \DateTimeZone::listIdentifiers(),
                        \DateTimeZone::listIdentifiers(),
                    ))
                    ->searchable()
                    ->required()
                    ->default('UTC'),
                Select::make('locale')
                    ->options([
                        'en'    => 'English',
                        'nl'    => 'Dutch',
                        'de'    => 'German',
                        'fr'    => 'French',
                        'es'    => 'Spanish',
                        'pt'    => 'Portuguese',
                        'nl-NL' => 'Dutch (Netherlands)',
                        'en-GB' => 'English (UK)',
                        'en-US' => 'English (US)',
                    ])
                    ->required()
                    ->default('en'),
                Select::make('currency')
                    ->options([
                        'EUR' => 'Euro (EUR)',
                        'USD' => 'US Dollar (USD)',
                        'GBP' => 'British Pound (GBP)',
                        'CHF' => 'Swiss Franc (CHF)',
                        'NOK' => 'Norwegian Krone (NOK)',
                        'SEK' => 'Swedish Krona (SEK)',
                        'DKK' => 'Danish Krone (DKK)',
                    ])
                    ->required()
                    ->default('EUR'),
            ])->columns(2),

            Section::make('Owner Details')
                ->schema([
                    TextInput::make('owner_first_name')
                        ->required()
                        ->maxLength(100)
                        ->label('First name'),
                    TextInput::make('owner_last_name')
                        ->required()
                        ->maxLength(100)
                        ->label('Last name'),
                    TextInput::make('owner_email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->label('Email address'),
                ])
                ->columns(3)
                ->visibleOn('create'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Company::withoutGlobalScopes()->withCount(['users', 'moduleSubscriptions']))
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('slug')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'trial'     => 'warning',
                        'active'    => 'success',
                        'suspended' => 'danger',
                        'cancelled' => 'gray',
                        default     => 'gray',
                    }),
                TextColumn::make('users_count')
                    ->label('Users')
                    ->sortable(),
                TextColumn::make('module_subscriptions_count')
                    ->label('Modules')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('trial_ends_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Trial ends')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'trial'     => 'Trial',
                        'active'    => 'Active',
                        'suspended' => 'Suspended',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('activate')
                    ->label('Activate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Company $record) => in_array($record->status, ['trial', 'suspended'], true))
                    ->action(function (Company $record): void {
                        app(CompanyService::class)->activate($record->id);
                        Notification::make()
                            ->title('Company activated')
                            ->success()
                            ->send();
                    }),
                Action::make('suspend')
                    ->label('Suspend')
                    ->icon('heroicon-o-pause-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Company $record) => $record->status === 'active')
                    ->action(function (Company $record): void {
                        app(CompanyService::class)->suspend($record->id);
                        Notification::make()
                            ->title('Company suspended')
                            ->warning()
                            ->send();
                    }),
                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('This will cancel the subscription. The company data will be retained but all access will be blocked.')
                    ->visible(fn (Company $record) => $record->status !== 'cancelled')
                    ->action(function (Company $record): void {
                        app(CompanyService::class)->cancel($record->id);
                        Notification::make()
                            ->title('Company cancelled')
                            ->danger()
                            ->send();
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCompanies::route('/'),
            'create' => CreateCompany::route('/create'),
            'view'   => ViewCompany::route('/{record}'),
            'edit'   => EditCompany::route('/{record}/edit'),
        ];
    }
}
