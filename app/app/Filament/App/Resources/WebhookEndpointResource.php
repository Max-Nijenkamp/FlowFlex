<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Actions\RotateWebhookSecretAction;
use App\Actions\SendTestWebhookAction;
use App\Contracts\BillingServiceInterface;
use App\Models\WebhookEndpoint;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use UnitEnum;

class WebhookEndpointResource extends Resource
{
    protected static ?string $model = WebhookEndpoint::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('core.webhooks.view-any')
            && app(BillingServiceInterface::class)->hasModule('core.webhooks');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('url')
                ->url()
                ->required()
                ->startsWith('https://')
                ->validationMessages(['starts_with' => 'Webhook URLs must use HTTPS.']),
            CheckboxList::make('events')
                ->options(fn () => collect(config('flowflex.webhook_events', []))
                    ->mapWithKeys(fn (string $class) => [class_basename($class) => class_basename($class)])
                    ->all())
                ->columns(2)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->columns([
                TextColumn::make('url')->searchable()->limit(50),
                TextColumn::make('events')->badge()->separator(','),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('consecutive_failures')->label('Failures'),
            ])
            ->recordActions([
                Action::make('test')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->visible(fn () => Auth::guard('web')->user()->can('core.webhooks.test'))
                    ->action(function (WebhookEndpoint $record): void {
                        // Throttled: 3 test sends per endpoint per minute (security notes).
                        $key = "webhook-test:{$record->id}";
                        if (RateLimiter::tooManyAttempts($key, 3)) {
                            Notification::make()->danger()->title('Too many test sends — try again in a minute.')->send();

                            return;
                        }
                        RateLimiter::hit($key, 60);

                        SendTestWebhookAction::run($record->id);
                        Notification::make()->success()->title('Test delivery queued')->send();
                    }),
                Action::make('rotateSecret')
                    ->icon(Heroicon::OutlinedKey)
                    ->requiresConfirmation()
                    ->visible(fn () => Auth::guard('web')->user()->can('core.webhooks.update'))
                    ->action(function (WebhookEndpoint $record): void {
                        $secret = RotateWebhookSecretAction::run($record->id);
                        Notification::make()
                            ->success()
                            ->title('New signing secret (copy now — shown once)')
                            ->body($secret)
                            ->persistent()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => WebhookEndpointResource\Pages\ListWebhookEndpoints::route('/'),
            'create' => WebhookEndpointResource\Pages\CreateWebhookEndpoint::route('/create'),
            'edit' => WebhookEndpointResource\Pages\EditWebhookEndpoint::route('/{record}/edit'),
        ];
    }
}
