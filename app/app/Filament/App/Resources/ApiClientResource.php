<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Actions\CreateApiTokenAction;
use App\Actions\RevokeApiTokenAction;
use App\Contracts\BillingServiceInterface;
use App\Data\CreateApiTokenData;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use UnitEnum;

class ApiClientResource extends Resource
{
    protected static ?string $model = PersonalAccessToken::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCommandLine;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $modelLabel = 'API token';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('core.api.view-any')
            && app(BillingServiceInterface::class)->hasModule('core.api');
    }

    public static function getEloquentQuery(): Builder
    {
        // Tokens of users in the current company only.
        $query = parent::getEloquentQuery();
        $query->where('tokenable_type', User::class)
            ->whereIn('tokenable_id', User::query()->pluck('id'));

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('abilities')->badge()->separator(','),
                TextColumn::make('last_used_at')->dateTime()->placeholder('Never'),
                TextColumn::make('expires_at')->dateTime()->placeholder('No expiry'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->headerActions([
                Action::make('create')
                    ->label('Create token')
                    ->visible(fn () => Auth::guard('web')->user()->can('core.api.create'))
                    ->schema([
                        TextInput::make('name')->required()->maxLength(100),
                        CheckboxList::make('abilities')
                            ->options(self::abilityOptions())
                            ->columns(2)
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $plain = CreateApiTokenAction::run(CreateApiTokenData::from($data));
                        Notification::make()
                            ->success()
                            ->title('API token (copy now — shown once)')
                            ->body($plain)
                            ->persistent()
                            ->send();
                    }),
            ])
            ->recordActions([
                Action::make('revoke')
                    ->color('danger')
                    ->icon(Heroicon::OutlinedTrash)
                    ->requiresConfirmation()
                    ->visible(fn () => Auth::guard('web')->user()->can('core.api.revoke'))
                    ->action(function (PersonalAccessToken $record): void {
                        RevokeApiTokenAction::run((string) $record->getKey());
                        Notification::make()->success()->title('Token revoked')->send();
                    }),
            ]);
    }

    /** @return array<string, string> abilities for active modules: {domain}:{read|write} */
    private static function abilityOptions(): array
    {
        $domains = collect(app(BillingServiceInterface::class)->activeModuleKeys())
            ->map(fn (string $key) => explode('.', $key)[0])
            ->unique();

        return $domains
            ->flatMap(fn (string $d) => ["{$d}:read" => "{$d}:read", "{$d}:write" => "{$d}:write"])
            ->all();
    }

    public static function getPages(): array
    {
        return [
            'index' => ApiClientResource\Pages\ListApiTokens::route('/'),
        ];
    }
}
