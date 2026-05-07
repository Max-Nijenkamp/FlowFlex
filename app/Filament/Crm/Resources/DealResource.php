<?php

namespace App\Filament\Crm\Resources;

use App\Enums\Crm\DealStatus;
use App\Events\Crm\DealLost;
use App\Events\Crm\DealWon;
use App\Filament\Crm\Enums\NavigationGroup;
use App\Filament\Crm\Resources\DealResource\Pages\CreateDeal;
use App\Filament\Crm\Resources\DealResource\Pages\EditDeal;
use App\Filament\Crm\Resources\DealResource\Pages\ListDeals;
use App\Filament\Crm\Resources\DealResource\RelationManagers\DealNotesRelationManager;
use App\Models\Crm\CrmCompany;
use App\Models\Crm\CrmContact;
use App\Models\Crm\Deal;
use App\Models\Crm\DealStage;
use App\Models\Crm\Pipeline;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DealResource extends Resource
{
    protected static ?string $model = Deal::class;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Sales->label();
    }

    public static function getModelLabel(): string
    {
        return __('crm.resources.deals.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm.resources.deals.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('crm.deals.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('crm.deals.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('crm.deals.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('crm.deals.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('crm.resources.deals.sections.details'))
                ->schema([
                    TextInput::make('title')
                        ->label(__('crm.resources.deals.fields.title'))
                        ->required()
                        ->maxLength(255),

                    Select::make('crm_contact_id')
                        ->label(__('crm.resources.deals.fields.contact'))
                        ->options(fn () => CrmContact::query()->get()->mapWithKeys(fn (CrmContact $c) => [$c->id => $c->full_name])->toArray())
                        ->nullable()
                        ->searchable(),

                    Select::make('crm_company_id')
                        ->label(__('crm.resources.deals.fields.company'))
                        ->options(fn () => CrmCompany::query()->pluck('name', 'id')->toArray())
                        ->nullable()
                        ->searchable(),

                    Select::make('pipeline_id')
                        ->label(__('crm.resources.deals.fields.pipeline'))
                        ->options(fn () => Pipeline::query()->pluck('name', 'id')->toArray())
                        ->nullable()
                        ->searchable()
                        ->live(),

                    Select::make('deal_stage_id')
                        ->label(__('crm.resources.deals.fields.stage'))
                        ->options(fn (\Filament\Schemas\Components\Utilities\Get $get) => DealStage::query()
                            ->when($get('pipeline_id'), fn ($q, $id) => $q->where('pipeline_id', $id))
                            ->pluck('name', 'id')
                            ->toArray()
                        )
                        ->nullable()
                        ->searchable(),

                    TextInput::make('value')
                        ->label(__('crm.resources.deals.fields.value'))
                        ->numeric()
                        ->nullable(),

                    Select::make('currency')
                        ->label(__('crm.resources.deals.fields.currency'))
                        ->options([
                            'EUR' => 'EUR',
                            'GBP' => 'GBP',
                            'USD' => 'USD',
                        ])
                        ->default('EUR'),

                    Select::make('status')
                        ->label(__('crm.resources.deals.fields.status'))
                        ->options(
                            collect(DealStatus::cases())
                                ->mapWithKeys(fn (DealStatus $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->default(DealStatus::Open->value)
                        ->required(),

                    TextInput::make('close_probability')
                        ->label(__('crm.resources.deals.fields.close_probability'))
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->nullable()
                        ->suffix('%'),

                    DatePicker::make('expected_close_date')
                        ->label(__('crm.resources.deals.fields.expected_close_date'))
                        ->nullable()
                        ->native(false),

                    Textarea::make('lost_reason')
                        ->label(__('crm.resources.deals.fields.lost_reason'))
                        ->nullable()
                        ->rows(2),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('contact.full_name')
                    ->label(__('crm.resources.deals.columns.contact'))
                    ->getStateUsing(fn (Deal $record) => $record->contact?->full_name)
                    ->placeholder('—'),

                TextColumn::make('value')
                    ->numeric(decimalPlaces: 2)
                    ->prefix(fn (Deal $record) => $record->currency . ' ')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?DealStatus $state) => $state?->label())
                    ->color(fn (?DealStatus $state) => $state?->color()),

                TextColumn::make('stage.name')
                    ->label(__('crm.resources.deals.columns.stage'))
                    ->placeholder('—'),

                TextColumn::make('expected_close_date')
                    ->label(__('crm.resources.deals.columns.expected_close_date'))
                    ->date('d M Y')
                    ->placeholder('—'),

                TextColumn::make('close_probability')
                    ->label(__('crm.resources.deals.columns.probability'))
                    ->suffix('%')
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->actions([
                Action::make('mark_won')
                    ->label(__('crm.resources.deals.actions.mark_won'))
                    ->color('success')
                    ->icon('heroicon-o-trophy')
                    ->visible(fn (Deal $record) => $record->status === DealStatus::Open)
                    ->requiresConfirmation()
                    ->action(function (Deal $record): void {
                        $record->update([
                            'status'    => DealStatus::Won,
                            'closed_at' => now(),
                        ]);
                        event(new DealWon($record));
                    }),

                Action::make('mark_lost')
                    ->label(__('crm.resources.deals.actions.mark_lost'))
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (Deal $record) => $record->status === DealStatus::Open)
                    ->form([
                        Textarea::make('lost_reason')
                            ->label(__('crm.resources.deals.actions.lost_reason'))
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Deal $record, array $data): void {
                        $record->update([
                            'status'      => DealStatus::Lost,
                            'closed_at'   => now(),
                            'lost_reason' => $data['lost_reason'],
                        ]);
                        event(new DealLost($record));
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['contact', 'stage', 'crmCompany']);
    }

    public static function getRelationManagers(): array
    {
        return [
            DealNotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListDeals::route('/'),
            'create' => CreateDeal::route('/create'),
            'edit'   => EditDeal::route('/{record}/edit'),
        ];
    }
}
