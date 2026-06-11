<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Contracts\BillingServiceInterface;
use App\Filament\Admin\Resources\CompanyResource\Pages;
use App\Filament\Admin\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Customers';

    public static function canAccess(): bool
    {
        return Auth::guard('admin')->check();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Company')
                ->columns(2)
                ->components([
                    TextInput::make('name')->required()->maxLength(120),
                    TextInput::make('slug')->disabled(),
                ]),
            Section::make('Subscription')
                ->columns(2)
                ->components([
                    Select::make('subscription_status')
                        ->options([
                            'trialing' => 'Trialing',
                            'active' => 'Active',
                            'suspended' => 'Suspended',
                        ])
                        ->required(),
                    DateTimePicker::make('trial_ends_at'),
                ]),
            Section::make('Localisation')
                ->columns(3)
                ->components([
                    TextInput::make('timezone')->required(),
                    TextInput::make('locale')->required()->maxLength(10),
                    TextInput::make('currency')->required()->length(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->modifyQueryUsing(fn ($query) => $query->withCount('users')->latest())
            ->columns([
                TextColumn::make('name')->searchable()->sortable()
                    ->description(fn (Company $r) => $r->slug),
                TextColumn::make('subscription_status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'trialing' => 'info',
                        default => 'danger',
                    }),
                TextColumn::make('users_count')->label('Users')->sortable(),
                TextColumn::make('active_modules')->label('Modules')
                    ->state(fn (Company $r): int => $r->subscriptions()->whereNull('deactivated_at')->count()),
                TextColumn::make('trial_ends_at')->dateTime()->sortable()->toggleable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('subscription_status')->options([
                    'trialing' => 'Trialing',
                    'active' => 'Active',
                    'suspended' => 'Suspended',
                ]),
            ])
            ->recordActions([
                Action::make('suspend')
                    ->icon(Heroicon::OutlinedNoSymbol)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Company $r) => $r->subscription_status !== 'suspended')
                    ->schema([
                        Textarea::make('reason')->required()->maxLength(500),
                    ])
                    ->action(function (Company $record, array $data): void {
                        app(BillingServiceInterface::class)->suspend($record->id, $data['reason']);
                        Notification::make()->success()->title('Company suspended')->send();
                    }),
                Action::make('reactivate')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Company $r) => $r->subscription_status === 'suspended')
                    ->action(function (Company $record): void {
                        $record->update(['subscription_status' => 'active']);
                        Notification::make()->success()->title('Company reactivated')->send();
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ModulesRelationManager::class,
            RelationManagers\InvoicesRelationManager::class,
            RelationManagers\UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
