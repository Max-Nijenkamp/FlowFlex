<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources;

use App\Actions\CRM\ConvertLeadAction;
use App\Contracts\BillingServiceInterface;
use App\Models\CRM\Lead;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use UnitEnum;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Contacts';

    protected static ?int $navigationSort = -1; // top of funnel — above Contacts

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('crm.leads.view-any')
            && app(BillingServiceInterface::class)->hasModule('crm.leads');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Lead')
                ->columns(2)
                ->components([
                    TextInput::make('name')->label('Contact name')->required()->maxLength(120),
                    TextInput::make('company_name')->label('Company')->maxLength(160),
                    TextInput::make('email')->email()->maxLength(160),
                    TextInput::make('phone')->tel()->maxLength(40),
                    Select::make('source')
                        ->options(['manual' => 'Manual', 'website' => 'Website', 'referral' => 'Referral', 'event' => 'Event', 'import' => 'Import'])
                        ->default('manual')
                        ->required(),
                    Select::make('status')
                        ->options(['new' => 'New', 'working' => 'Working', 'qualified' => 'Qualified', 'unqualified' => 'Unqualified'])
                        ->default('new')
                        ->required()
                        ->disabled(fn (?Lead $record): bool => $record?->isConverted() ?? false),
                    TextInput::make('estimated_value_cents')->label('Estimated value (€)')->numeric()->minValue(0)
                        ->formatStateUsing(fn ($state): float => $state === null ? 0.0 : round($state / 100, 2))
                        ->dehydrateStateUsing(fn ($state): int => (int) round((float) $state * 100)),
                    Select::make('owner_id')->label('Owner')
                        ->options(fn () => User::query()->get()->pluck('full_name', 'id'))
                        ->searchable()
                        ->default(fn () => Auth::guard('web')->id()),
                    Textarea::make('notes')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->emptyStateHeading('No leads yet')
            ->emptyStateDescription('Capture a prospect here, work it, then convert the qualified ones straight into a pipeline deal.')
            ->columns([
                TextColumn::make('name')->searchable()->weight('semibold'),
                TextColumn::make('company_name')->label('Company')->searchable()->placeholder('—'),
                TextColumn::make('source')->badge(),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'qualified' => 'success',
                        'converted' => 'info',
                        'unqualified' => 'danger',
                        'working' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('estimated_value_cents')->label('Value')
                    ->formatStateUsing(fn (int $state): string => '€'.number_format($state / 100, 0))
                    ->sortable(),
                TextColumn::make('owner.full_name')->label('Owner')->placeholder('Unassigned'),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(['new' => 'New', 'working' => 'Working', 'qualified' => 'Qualified', 'unqualified' => 'Unqualified', 'converted' => 'Converted']),
                SelectFilter::make('source')
                    ->options(['manual' => 'Manual', 'website' => 'Website', 'referral' => 'Referral', 'event' => 'Event', 'import' => 'Import']),
            ])
            ->recordActions([
                Action::make('convert')
                    ->label('Convert to deal')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('primary')
                    ->visible(fn (Lead $record): bool => ! $record->isConverted()
                        && Auth::guard('web')->user()->can('crm.leads.convert'))
                    ->requiresConfirmation()
                    ->modalHeading('Convert lead to deal')
                    ->modalDescription('Creates a deal in the default pipeline and links a contact. The lead is marked converted.')
                    ->action(function (Lead $record): void {
                        try {
                            $deal = ConvertLeadAction::run($record);
                            Notification::make()->success()
                                ->title('Lead converted')
                                ->body("Deal “{$deal->name}” created in the pipeline.")
                                ->send();
                        } catch (ValidationException $e) {
                            Notification::make()->danger()
                                ->title('Could not convert')
                                ->body(implode(' ', collect($e->errors())->flatten()->all()))
                                ->send();
                        }
                    }),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => LeadResource\Pages\ListLeads::route('/'),
        ];
    }
}
