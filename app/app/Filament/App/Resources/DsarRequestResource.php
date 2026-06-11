<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Actions\CreateDsarRequestAction;
use App\Contracts\BillingServiceInterface;
use App\Data\CreateDsarRequestData;
use App\Jobs\ProcessAccessRequestJob;
use App\Jobs\ProcessErasureRequestJob;
use App\Models\DsarRequest;
use App\States\DsarRequest\InProgress;
use App\States\DsarRequest\Received;
use App\States\DsarRequest\Rejected;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class DsarRequestResource extends Resource
{
    protected static ?string $model = DsarRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLockClosed;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $modelLabel = 'DSAR request';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('core.privacy.view-any')
            && app(BillingServiceInterface::class)->hasModule('core.privacy');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('subject_email')->email()->required(),
            Select::make('request_type')->options(['access' => 'Access', 'erasure' => 'Erasure'])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('subject_email')->searchable(),
                TextColumn::make('request_type')->badge(),
                TextColumn::make('status')->badge()
                    ->color(fn ($state): string => match ((string) $state) {
                        'completed' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('due_at')->dateTime()->sortable()
                    ->description(fn (DsarRequest $r) => $r->completed_at === null ? $r->due_at->diffForHumans() : null)
                    ->color(fn (DsarRequest $r) => $r->completed_at === null && $r->due_at->isPast() ? 'danger' : null),
                TextColumn::make('completed_at')->dateTime()->placeholder('—'),
            ])
            ->headerActions([
                Action::make('create')
                    ->label('Log DSAR request')
                    ->visible(fn () => Auth::guard('web')->user()->can('core.privacy.manage'))
                    ->schema([
                        TextInput::make('subject_email')->email()->required(),
                        Select::make('request_type')->options(['access' => 'Access', 'erasure' => 'Erasure'])->required(),
                    ])
                    ->action(function (array $data): void {
                        CreateDsarRequestAction::run(CreateDsarRequestData::from($data));
                        Notification::make()->success()->title('DSAR request logged — 30-day clock started')->send();
                    }),
            ])
            ->recordActions([
                Action::make('process')
                    ->icon(Heroicon::OutlinedPlay)
                    ->visible(fn (DsarRequest $r) => $r->status->equals(Received::class)
                        && Auth::guard('web')->user()->can('core.privacy.manage'))
                    ->requiresConfirmation()
                    ->action(function (DsarRequest $record): void {
                        $record->request_type === 'access'
                            ? ProcessAccessRequestJob::dispatch($record->id)
                            : ProcessErasureRequestJob::dispatch($record->id);
                        Notification::make()->success()->title('Processing queued')->send();
                    }),
                Action::make('reject')
                    ->icon(Heroicon::OutlinedXMark)
                    ->color('danger')
                    ->visible(fn (DsarRequest $r) => ! $r->status->equals(Rejected::class)
                        && $r->completed_at === null
                        && Auth::guard('web')->user()->can('core.privacy.manage'))
                    ->schema([TextInput::make('reason')->required()])
                    ->action(function (DsarRequest $record, array $data): void {
                        if ($record->status->equals(Received::class)) {
                            $record->status->transitionTo(InProgress::class);
                        }
                        $record->forceFill(['rejection_reason' => $data['reason']])->save();
                        $record->status->transitionTo(Rejected::class);
                        Notification::make()->success()->title('Request rejected — reason recorded')->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => DsarRequestResource\Pages\ListDsarRequests::route('/'),
        ];
    }
}
