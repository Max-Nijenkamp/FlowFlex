<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\CRM\Activity;
use App\Models\CRM\Contact;
use App\Models\CRM\Deal;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static string|UnitEnum|null $navigationGroup = 'Contacts';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('crm.activities.view-any')
            && app(BillingServiceInterface::class)->hasModule('crm.activities');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')
                ->options(['call' => 'Call', 'email' => 'Email', 'meeting' => 'Meeting', 'note' => 'Note', 'task' => 'Task'])
                ->required(),
            TextInput::make('subject')->required()->maxLength(200),
            Textarea::make('body')->maxLength(5000),
            Select::make('contact_id')->label('Contact')
                ->options(fn () => Contact::query()->get()->pluck('full_name', 'id'))->nullable(),
            Select::make('deal_id')->label('Deal')
                ->options(fn () => Deal::query()->pluck('name', 'id'))->nullable(),
            DateTimePicker::make('due_at'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('type')->badge(),
                TextColumn::make('subject')->searchable(),
                TextColumn::make('contact.full_name')->label('Contact')
                    ->state(fn (Activity $r) => $r->contact?->full_name)->placeholder('—'),
                TextColumn::make('due_at')->dateTime()->placeholder('—'),
                TextColumn::make('completed_at')->dateTime()->placeholder('Open'),
            ])
            ->recordActions([
                Action::make('complete')
                    ->icon(Heroicon::OutlinedCheck)
                    ->visible(fn (Activity $r) => $r->completed_at === null)
                    ->action(fn (Activity $record) => $record->update(['completed_at' => now()])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ActivityResource\Pages\ListActivities::route('/'),
            'create' => ActivityResource\Pages\CreateActivity::route('/create'),
        ];
    }
}
