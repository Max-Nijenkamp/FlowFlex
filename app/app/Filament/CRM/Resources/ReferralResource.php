<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\CRM\Contact;
use App\Models\CRM\Referral;
use App\Models\CRM\ReferralProgram;
use App\Services\CRM\ReferralService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use UnitEnum;

class ReferralResource extends Resource
{
    protected static ?string $model = Referral::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGift;

    protected static string|UnitEnum|null $navigationGroup = 'Growth';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('crm.referrals.view-any')
            && app(BillingServiceInterface::class)->hasModule('crm.referrals');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Referral')
                ->columns(2)
                ->components([
                    Select::make('program_id')->label('Program')
                        ->options(fn () => ReferralProgram::query()->pluck('name', 'id'))
                        ->required(),
                    Select::make('referrer_contact_id')->label('Referred by')
                        ->options(fn () => Contact::query()->orderBy('last_name')->get()->pluck('full_name', 'id'))
                        ->searchable()
                        ->required(),
                    TextInput::make('referee_email')->label('Referee email')->email()->required(),
                    TextInput::make('referral_code')->label('Code')->required()
                        ->default(fn () => strtoupper(Str::random(8))),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('referral_code'),
                TextColumn::make('referee_email')->placeholder('—'),
                TextColumn::make('status')->badge(),
                TextColumn::make('converted_at')->dateTime()->placeholder('—'),
            ])
            ->recordActions([
                Action::make('qualify')
                    ->icon(Heroicon::OutlinedCheck)->color('success')
                    ->visible(fn (Referral $r) => $r->status === 'pending' && $r->referee_email !== ''
                        && Auth::guard('web')->user()->can('crm.referrals.qualify'))
                    ->requiresConfirmation()
                    ->action(function (Referral $record): void {
                        app(ReferralService::class)->qualify($record->id);
                        Notification::make()->success()->title('Referral qualified')->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ReferralResource\Pages\ListReferrals::route('/'),
        ];
    }
}
