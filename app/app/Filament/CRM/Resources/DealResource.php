<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources;

use App\Contracts\BillingServiceInterface;
use App\Contracts\CRM\DealServiceInterface;
use App\Models\CRM\Contact;
use App\Models\CRM\Deal;
use App\Models\CRM\PipelineStage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class DealResource extends Resource
{
    protected static ?string $model = Deal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static string|UnitEnum|null $navigationGroup = 'Sales';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('crm.deals.view-any')
            && app(BillingServiceInterface::class)->hasModule('crm.deals');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(200),
            Select::make('stage_id')->label('Stage')
                ->options(fn () => PipelineStage::query()->orderBy('order')->pluck('name', 'id'))
                ->required(),
            TextInput::make('value_cents')->numeric()->required()->label('Value (cents)'),
            Select::make('contact_id')->label('Contact')
                ->options(fn () => Contact::query()->get()->pluck('full_name', 'id'))
                ->nullable(),
            DatePicker::make('expected_close_date'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('stage.name')->label('Stage')->badge(),
                TextColumn::make('value_cents')->label('Value')
                    ->formatStateUsing(fn (int $state, Deal $r) => number_format($state / 100, 2).' '.$r->currency),
                TextColumn::make('probability')->suffix('%'),
                TextColumn::make('status')->badge()
                    ->color(fn ($state): string => match ((string) $state) {
                        'won' => 'success', 'lost' => 'danger', default => 'info',
                    }),
                TextColumn::make('expected_close_date')->date()->placeholder('—'),
            ])
            ->recordActions([
                Action::make('win')
                    ->icon(Heroicon::OutlinedTrophy)->color('success')
                    ->visible(fn (Deal $r) => (string) $r->status === 'open'
                        && Auth::guard('web')->user()->can('crm.deals.win'))
                    ->requiresConfirmation()
                    ->action(function (Deal $record): void {
                        app(DealServiceInterface::class)->win($record->id);
                        Notification::make()->success()->title('Deal won! 🎉')->send();
                    }),
                Action::make('lose')
                    ->icon(Heroicon::OutlinedHandThumbDown)->color('danger')
                    ->visible(fn (Deal $r) => (string) $r->status === 'open'
                        && Auth::guard('web')->user()->can('crm.deals.lose'))
                    ->schema([Textarea::make('lost_reason')->required()->maxLength(1000)])
                    ->action(function (Deal $record, array $data): void {
                        app(DealServiceInterface::class)->lose($record->id, $data['lost_reason']);
                        Notification::make()->success()->title('Deal marked lost')->send();
                    }),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => DealResource\Pages\ListDeals::route('/'),
            'create' => DealResource\Pages\CreateDeal::route('/create'),
            'edit' => DealResource\Pages\EditDeal::route('/{record}/edit'),
        ];
    }
}
