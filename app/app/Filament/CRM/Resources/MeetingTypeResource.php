<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\CRM\MeetingType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class MeetingTypeResource extends Resource
{
    protected static ?string $model = MeetingType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Scheduling';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('crm.scheduling.manage-types')
            && app(BillingServiceInterface::class)->hasModule('crm.scheduling');
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
                TextColumn::make('name')->searchable(),
                TextColumn::make('slug')->label('Booking URL')->formatStateUsing(fn (string $state) => url('/book/'.$state)),
                TextColumn::make('duration_minutes')->label('Duration'),
                TextColumn::make('price_cents')->label('Price')->formatStateUsing(fn (int $state) => '€'.number_format($state / 100, 2)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => MeetingTypeResource\Pages\ListMeetingTypes::route('/'),
        ];
    }
}
