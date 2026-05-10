<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ApiClientResource\Pages\CreateApiClient;
use App\Filament\App\Resources\ApiClientResource\Pages\EditApiClient;
use App\Filament\App\Resources\ApiClientResource\Pages\ListApiClients;
use App\Models\Core\ApiClient;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ApiClientResource extends Resource
{
    protected static ?string $model = ApiClient::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-key';
    }

    public static function getNavigationGroup(): string
    {
        return 'Developers';
    }

    public static function getNavigationLabel(): string
    {
        return 'API Clients';
    }

    public static function getNavigationSort(): int
    {
        return 10;
    }

    public static function canAccess(): bool
    {
        return auth()->check()
            && auth()->user()->checkPermissionTo('core.api.manage-clients');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Client Details')->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Client name'),
                Textarea::make('description')
                    ->rows(3)
                    ->maxLength(1000)
                    ->label('Description'),
                TagsInput::make('scopes')
                    ->label('Scopes')
                    ->placeholder('Add scope')
                    ->helperText('e.g. read:employees write:timesheets'),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->hiddenOn('create'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('client_id')
                    ->label('Client ID')
                    ->formatStateUsing(fn (string $state): string => substr($state, 0, 12) . '...')
                    ->copyable()
                    ->tooltip(fn ($record) => $record->client_id),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('last_used_at')
                    ->label('Last used')
                    ->dateTime()
                    ->placeholder('Never')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->date()
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
                Action::make('revoke')
                    ->label('Revoke')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (ApiClient $record) => $record->is_active)
                    ->action(function (ApiClient $record): void {
                        $record->update(['is_active' => false]);
                        Notification::make()
                            ->title('API client revoked')
                            ->warning()
                            ->send();
                    }),
                Action::make('regenerate_secret')
                    ->label('Regenerate secret')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('This will invalidate the current secret. Any integrations using it will stop working immediately.')
                    ->action(function (ApiClient $record): void {
                        $plaintext = Str::random(64);
                        $record->update(['client_secret' => hash('sha256', $plaintext)]);
                        Notification::make()
                            ->title('Secret regenerated')
                            ->body("Save this secret now — it won't be shown again: {$plaintext}")
                            ->warning()
                            ->persistent()
                            ->send();
                    }),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListApiClients::route('/'),
            'create' => CreateApiClient::route('/create'),
            'edit'   => EditApiClient::route('/{record}/edit'),
        ];
    }
}
