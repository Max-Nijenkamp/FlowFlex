<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\CRM\DealRoom;
use App\Services\CRM\DealRoomService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class DealRoomResource extends Resource
{
    protected static ?string $model = DealRoom::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;

    protected static string|UnitEnum|null $navigationGroup = 'Sales';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('crm.deal-rooms.view-any')
            && app(BillingServiceInterface::class)->hasModule('crm.deal-rooms');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('deal.name')->label('Deal'),
                TextColumn::make('access_token')->label('Public link')->formatStateUsing(fn (string $state) => url('/room/'.$state))->copyable(),
                TextColumn::make('expires_at')->dateTime(),
                TextColumn::make('revoked_at')->dateTime()->placeholder('live'),
            ])
            ->recordActions([
                Action::make('revoke')
                    ->icon(Heroicon::OutlinedNoSymbol)->color('danger')
                    ->visible(fn (DealRoom $r) => $r->revoked_at === null
                        && Auth::guard('web')->user()->can('crm.deal-rooms.revoke'))
                    ->requiresConfirmation()
                    ->action(function (DealRoom $record): void {
                        app(DealRoomService::class)->revoke($record->id);
                        Notification::make()->success()->title('Room link revoked')->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => DealRoomResource\Pages\ListDealRooms::route('/'),
        ];
    }
}
