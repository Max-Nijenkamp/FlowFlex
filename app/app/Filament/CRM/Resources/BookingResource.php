<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\CRM\Booking;
use App\Models\CRM\Contact;
use App\Models\CRM\MeetingType;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static string|UnitEnum|null $navigationGroup = 'Scheduling';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('crm.scheduling.view-any')
            && app(BillingServiceInterface::class)->hasModule('crm.scheduling');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Booking')
                ->columns(2)
                ->components([
                    Select::make('meeting_type_id')->label('Meeting type')
                        ->options(fn () => MeetingType::query()->pluck('name', 'id'))
                        ->required(),
                    Select::make('contact_id')->label('Contact')
                        ->options(fn () => Contact::query()->orderBy('last_name')->get()->pluck('full_name', 'id'))
                        ->searchable()
                        ->required(),
                    DateTimePicker::make('scheduled_at')->label('Scheduled for')->required(),
                    Hidden::make('assigned_rep_id')->default(fn () => Auth::guard('web')->id()),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('meetingType.name')->label('Type'),
                TextColumn::make('scheduled_at')->dateTime()->sortable(),
                TextColumn::make('status')->badge(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => BookingResource\Pages\ListBookings::route('/'),
        ];
    }
}
