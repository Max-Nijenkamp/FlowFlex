<?php

namespace App\Filament\Crm\Resources;

use App\Enums\Crm\TicketPriority;
use App\Filament\Crm\Enums\NavigationGroup;
use App\Filament\Crm\Resources\TicketSlaRuleResource\Pages\CreateTicketSlaRule;
use App\Filament\Crm\Resources\TicketSlaRuleResource\Pages\EditTicketSlaRule;
use App\Filament\Crm\Resources\TicketSlaRuleResource\Pages\ListTicketSlaRules;
use App\Models\Crm\TicketSlaRule;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TicketSlaRuleResource extends Resource
{
    protected static ?string $model = TicketSlaRule::class;

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Support->label();
    }

    public static function getModelLabel(): string
    {
        return __('crm.resources.ticket_sla_rules.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm.resources.ticket_sla_rules.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('crm.ticket-sla-rules.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('crm.ticket-sla-rules.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('crm.ticket-sla-rules.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('crm.ticket-sla-rules.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('crm.resources.ticket_sla_rules.sections.details'))
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    Select::make('priority')
                        ->label(__('crm.resources.ticket_sla_rules.fields.priority'))
                        ->options(
                            collect(TicketPriority::cases())
                                ->mapWithKeys(fn (TicketPriority $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->default(TicketPriority::Normal->value)
                        ->required(),

                    TextInput::make('first_response_hours')
                        ->label(__('crm.resources.ticket_sla_rules.fields.first_response_hours'))
                        ->numeric()
                        ->minValue(1)
                        ->required(),

                    TextInput::make('resolution_hours')
                        ->label(__('crm.resources.ticket_sla_rules.fields.resolution_hours'))
                        ->numeric()
                        ->minValue(1)
                        ->required(),

                    Toggle::make('is_active')
                        ->label(__('crm.resources.ticket_sla_rules.fields.is_active'))
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('priority')
                    ->badge()
                    ->formatStateUsing(fn (?TicketPriority $state) => $state?->label())
                    ->color(fn (?TicketPriority $state) => $state?->color()),

                TextColumn::make('first_response_hours')
                    ->label(__('crm.resources.ticket_sla_rules.columns.first_response_hours'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('resolution_hours')
                    ->label(__('crm.resources.ticket_sla_rules.columns.resolution_hours'))
                    ->numeric()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('crm.resources.ticket_sla_rules.columns.is_active'))
                    ->boolean(),
            ])
            ->defaultSort('name')
            ->striped()
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTicketSlaRules::route('/'),
            'create' => CreateTicketSlaRule::route('/create'),
            'edit'   => EditTicketSlaRule::route('/{record}/edit'),
        ];
    }
}
