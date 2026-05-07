<?php

namespace App\Filament\Crm\Resources;

use App\Enums\Crm\TicketPriority;
use App\Enums\Crm\TicketStatus;
use App\Events\Crm\TicketResolved;
use App\Filament\Crm\Enums\NavigationGroup;
use App\Filament\Crm\Resources\TicketResource\Pages\CreateTicket;
use App\Filament\Crm\Resources\TicketResource\Pages\EditTicket;
use App\Filament\Crm\Resources\TicketResource\Pages\ListTickets;
use App\Filament\Crm\Resources\TicketResource\RelationManagers\MessagesRelationManager;
use App\Models\Crm\CrmCompany;
use App\Models\Crm\CrmContact;
use App\Models\Crm\Ticket;
use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Support->label();
    }

    public static function getModelLabel(): string
    {
        return __('crm.resources.tickets.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm.resources.tickets.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('crm.tickets.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('crm.tickets.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('crm.tickets.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('crm.tickets.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('crm.resources.tickets.sections.details'))
                ->schema([
                    TextInput::make('subject')
                        ->label(__('crm.resources.tickets.fields.subject'))
                        ->required()
                        ->maxLength(255),

                    Select::make('crm_contact_id')
                        ->label(__('crm.resources.tickets.fields.contact'))
                        ->options(fn () => CrmContact::query()->get()->mapWithKeys(fn (CrmContact $c) => [$c->id => $c->full_name])->toArray())
                        ->nullable()
                        ->searchable(),

                    Select::make('crm_company_id')
                        ->label(__('crm.resources.tickets.fields.company'))
                        ->options(fn () => CrmCompany::query()->pluck('name', 'id')->toArray())
                        ->nullable()
                        ->searchable(),

                    Select::make('priority')
                        ->label(__('crm.resources.tickets.fields.priority'))
                        ->options(
                            collect(TicketPriority::cases())
                                ->mapWithKeys(fn (TicketPriority $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->default(TicketPriority::Normal->value)
                        ->required(),

                    Select::make('status')
                        ->label(__('crm.resources.tickets.fields.status'))
                        ->options(
                            collect(TicketStatus::cases())
                                ->mapWithKeys(fn (TicketStatus $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->default(TicketStatus::Open->value)
                        ->required(),

                    Select::make('assigned_to')
                        ->label(__('crm.resources.tickets.fields.assigned_to'))
                        ->options(
                            fn () => Tenant::query()
                                ->where('company_id', auth()->user()?->company_id)
                                ->get()
                                ->mapWithKeys(fn (Tenant $tenant) => [
                                    $tenant->id => trim($tenant->first_name . ' ' . $tenant->last_name) ?: $tenant->email,
                                ])
                                ->toArray()
                        )
                        ->nullable()
                        ->searchable(),

                    DateTimePicker::make('sla_breach_at')
                        ->label(__('crm.resources.tickets.fields.sla_breach_at'))
                        ->nullable()
                        ->native(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('contact.full_name')
                    ->label(__('crm.resources.tickets.columns.contact'))
                    ->getStateUsing(fn (Ticket $record) => $record->contact?->full_name)
                    ->placeholder('—'),

                TextColumn::make('priority')
                    ->badge()
                    ->formatStateUsing(fn (?TicketPriority $state) => $state?->label())
                    ->color(fn (?TicketPriority $state) => $state?->color()),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?TicketStatus $state) => $state?->label())
                    ->color(fn (?TicketStatus $state) => $state?->color()),

                TextColumn::make('assignedTo.full_name')
                    ->label(__('crm.resources.tickets.columns.assigned_to'))
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label(__('crm.resources.tickets.columns.created'))
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->actions([
                Action::make('resolve')
                    ->label(__('crm.resources.tickets.actions.resolve'))
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (Ticket $record) => ! in_array($record->status, [TicketStatus::Resolved, TicketStatus::Closed]) && (auth()->user()?->can('crm.tickets.resolve') ?? false))
                    ->requiresConfirmation()
                    ->action(function (Ticket $record): void {
                        $record->update([
                            'status'      => TicketStatus::Resolved,
                            'resolved_at' => now(),
                        ]);
                        event(new TicketResolved($record));
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
        return parent::getEloquentQuery()->with(['contact', 'crmCompany', 'assignedTo']);
    }

    public static function getRelationManagers(): array
    {
        return [
            MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTickets::route('/'),
            'create' => CreateTicket::route('/create'),
            'edit'   => EditTicket::route('/{record}/edit'),
        ];
    }
}
