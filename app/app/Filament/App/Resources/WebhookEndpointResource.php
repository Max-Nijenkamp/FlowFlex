<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\WebhookEndpointResource\Pages\CreateWebhookEndpoint;
use App\Filament\App\Resources\WebhookEndpointResource\Pages\EditWebhookEndpoint;
use App\Filament\App\Resources\WebhookEndpointResource\Pages\ListWebhookEndpoints;
use App\Models\Core\WebhookEndpoint;
use App\Support\Services\CompanyContext;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class WebhookEndpointResource extends Resource
{
    protected static ?string $model = WebhookEndpoint::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-arrow-path';
    }

    public static function getNavigationGroup(): string
    {
        return 'Integrations';
    }

    public static function getNavigationLabel(): string
    {
        return 'Webhooks';
    }

    public static function getNavigationSort(): int
    {
        return 2;
    }

    public static function canAccess(): bool
    {
        return auth()->check() && app(CompanyContext::class)->hasCompany();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Endpoint')->components([
                TextInput::make('url')
                    ->label('Endpoint URL')
                    ->required()
                    ->url()
                    ->maxLength(2048)
                    ->placeholder('https://example.com/webhooks/flowflex'),

                Select::make('events')
                    ->label('Events')
                    ->multiple()
                    ->required()
                    ->options([
                        'project.created'  => 'project.created',
                        'task.created'     => 'task.created',
                        'task.updated'     => 'task.updated',
                        'employee.created' => 'employee.created',
                    ])
                    ->placeholder('Select events to subscribe to'),

                TextInput::make('secret')
                    ->label('Signing secret')
                    ->password()
                    ->revealable()
                    ->helperText('Leave blank to auto-generate a secret.')
                    ->dehydrateStateUsing(fn (?string $state): string => filled($state) ? $state : Str::random(40)),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    ->limit(60)
                    ->tooltip(fn (WebhookEndpoint $record) => $record->url),

                TagsColumn::make('events')
                    ->label('Events'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('last_triggered_at')
                    ->label('Last triggered')
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
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListWebhookEndpoints::route('/'),
            'create' => CreateWebhookEndpoint::route('/create'),
            'edit'   => EditWebhookEndpoint::route('/{record}/edit'),
        ];
    }
}
