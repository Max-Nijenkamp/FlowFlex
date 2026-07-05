<?php

declare(strict_types=1);

namespace App\Filament\Crm\Resources;

use App\Contracts\Crm\DealServiceInterface;
use App\Data\Crm\CloseDealData;
use App\Models\Crm\Account;
use App\Models\Crm\Contact;
use App\Models\Crm\Deal;
use App\Models\Crm\PipelineStage;
use App\Models\User;
use App\Services\BillingService;
use Brick\Money\Money;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Throwable;

/**
 * Deal records (crm.deals). Value entered in euros, stored as integer
 * cents; closing runs through DealService (state machine + events);
 * closed deals are read-only.
 */
class DealResource extends Resource
{
    protected static ?string $model = Deal::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Pipeline';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('crm.deals.view-any')
            && app(BillingService::class)->hasModule('crm.deals');
    }

    public static function canEdit(mixed $record): bool
    {
        return $record instanceof Deal && ! $record->isClosed();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Deal')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->maxLength(160),
                    TextInput::make('value_cents')
                        ->label('Value (€)')
                        ->numeric()
                        ->minValue(0)
                        ->required()
                        ->formatStateUsing(fn (?int $state): string => number_format(($state ?? 0) / 100, 2, '.', ''))
                        ->dehydrateStateUsing(fn (string|int|float|null $state): int => (int) round(((float) ($state ?? 0)) * 100)),
                    Select::make('stage_id')
                        ->label('Stage')
                        ->options(fn (): array => PipelineStage::query()
                            ->where('is_won', false)->where('is_lost', false)
                            ->orderBy('order')->pluck('name', 'id')->all())
                        ->required(),
                    Select::make('contact_id')
                        ->label('Primary contact')
                        ->options(fn (): array => Contact::query()->get()->sortBy('last_name')->mapWithKeys(
                            fn (Contact $contact): array => [$contact->id => $contact->full_name],
                        )->all())
                        ->searchable()
                        ->placeholder('None'),
                    Select::make('account_id')
                        ->label('Account')
                        ->options(fn (): array => Account::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->placeholder('None'),
                    Select::make('owner_id')
                        ->label('Owner')
                        ->options(fn (): array => User::query()->get()->mapWithKeys(
                            fn (User $user): array => [$user->id => $user->full_name],
                        )->all())
                        ->default(fn () => Auth::id())
                        ->required(),
                    DatePicker::make('expected_close_date')->native(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('value_cents')
                    ->label('Value')
                    ->formatStateUsing(fn (Deal $record): string => Money::ofMinor($record->value_cents, $record->currency)->formatToLocale('nl_NL'))
                    ->sortable(),
                TextColumn::make('stage.name')->label('Stage'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (Deal $record): string => str((string) $record->status)->headline()->toString())
                    ->color(fn (Deal $record): string => match ((string) $record->status) {
                        'won' => 'success', 'lost' => 'danger', default => 'info',
                    }),
                TextColumn::make('probability')
                    ->formatStateUsing(fn (Deal $record): string => rtrim(rtrim((string) $record->probability, '0'), '.').'%'),
                TextColumn::make('account.name')->label('Account')->placeholder('—'),
                TextColumn::make('owner.full_name')->label('Owner'),
                TextColumn::make('expected_close_date')->label('Close date')->date('d M Y')->placeholder('—')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options(['open' => 'Open', 'won' => 'Won', 'lost' => 'Lost']),
                SelectFilter::make('stage_id')
                    ->label('Stage')
                    ->options(fn (): array => PipelineStage::query()->orderBy('order')->pluck('name', 'id')->all()),
                SelectFilter::make('owner_id')
                    ->label('Owner')
                    ->options(fn (): array => User::query()->get()->mapWithKeys(
                        fn (User $user): array => [$user->id => $user->full_name],
                    )->all()),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->visible(fn (Deal $record): bool => ! $record->isClosed()),
                    self::closeDealAction(),
                    Action::make('duplicate')
                        ->icon('heroicon-o-document-duplicate')
                        ->visible(function (): bool {
                            $user = Auth::user();

                            return $user instanceof User && $user->can('crm.deals.create');
                        })
                        ->requiresConfirmation()
                        ->modalDescription('Copies the deal with its contacts and line items into the first open stage.')
                        ->action(function (Deal $record): void {
                            app(DealServiceInterface::class)->duplicate($record->id);
                            Notification::make()->success()->title('Deal duplicated')->send();
                        }),
                ]),
            ])
            ->emptyStateHeading('No deals yet')
            ->emptyStateDescription('Create a deal or add one straight from the pipeline board.');
    }

    public static function closeDealAction(): Action
    {
        return Action::make('closeDeal')
            ->label('Close deal')
            ->icon('heroicon-o-flag')
            ->color('warning')
            ->visible(function (Deal $record): bool {
                $user = Auth::user();

                return ! $record->isClosed() && $user instanceof User && $user->can('crm.deals.update');
            })
            ->schema([
                Select::make('outcome')
                    ->options(['won' => 'Won 🎉', 'lost' => 'Lost'])
                    ->live()
                    ->required(),
                Textarea::make('lost_reason')
                    ->rows(2)
                    ->visible(fn (callable $get): bool => $get('outcome') === 'lost')
                    ->requiredIf('outcome', 'lost'),
                TextInput::make('lost_to')
                    ->label('Lost to (competitor)')
                    ->visible(fn (callable $get): bool => $get('outcome') === 'lost'),
            ])
            ->requiresConfirmation()
            ->modalDescription('Closing is final — a closed deal never reopens; duplicate it to start a new cycle.')
            ->action(function (Deal $record, array $data): void {
                try {
                    app(DealServiceInterface::class)->close(new CloseDealData(
                        dealId: $record->id,
                        outcome: $data['outcome'],
                        lostReason: $data['lost_reason'] ?? null,
                        lostTo: $data['lost_to'] ?? null,
                    ));
                    Notification::make()->success()
                        ->title($data['outcome'] === 'won' ? 'Deal won 🎉' : 'Deal closed as lost')
                        ->send();
                } catch (Throwable $e) {
                    Notification::make()->danger()->title($e->getMessage())->send();
                }
            });
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
