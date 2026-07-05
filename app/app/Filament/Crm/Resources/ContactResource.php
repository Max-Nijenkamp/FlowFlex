<?php

declare(strict_types=1);

namespace App\Filament\Crm\Resources;

use App\Contracts\Crm\ContactServiceInterface;
use App\Models\Crm\Account;
use App\Models\Crm\Contact;
use App\Models\User;
use App\Services\BillingService;
use App\Support\Services\CompanyContext;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Person records — the CRM anchor (crm.contacts). Lifecycle is a plain
 * enum badge with a quick-move action; merge is the duplicate-resolution
 * row action; email uniqueness per company is the dedup guard.
 */
class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user';

    protected static string|\UnitEnum|null $navigationGroup = 'Contacts';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('crm.contacts.view-any')
            && app(BillingService::class)->hasModule('crm.contacts');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contact')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('first_name')->required()->maxLength(120),
                    TextInput::make('last_name')->required()->maxLength(120),
                    TextInput::make('email')
                        ->email()
                        ->rule(fn (?Contact $record) => Rule::unique('crm_contacts', 'email')
                            ->where('company_id', app(CompanyContext::class)->currentId())
                            ->whereNull('deleted_at')
                            ->ignore($record?->id))
                        ->validationMessages(['unique' => 'A contact with this email already exists.']),
                    TextInput::make('phone')->tel(),
                    TextInput::make('job_title')->maxLength(120),
                    Select::make('account_id')
                        ->label('Account')
                        ->options(fn (): array => Account::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->placeholder('No account'),
                    Select::make('lifecycle_stage')
                        ->options(self::lifecycleOptions())
                        ->default('lead')
                        ->required()
                        ->in(Contact::LIFECYCLE_STAGES),
                    Select::make('source')
                        ->options([
                            'website' => 'Website', 'referral' => 'Referral', 'linkedin' => 'LinkedIn',
                            'manual' => 'Manual', 'form' => 'Form', 'event' => 'Event',
                        ])
                        ->placeholder('Unknown'),
                    Select::make('owner_id')
                        ->label('Owner')
                        ->options(fn (): array => User::query()->get()->mapWithKeys(
                            fn (User $user): array => [$user->id => $user->full_name],
                        )->all())
                        ->default(fn () => Auth::id())
                        ->required(),
                    KeyValue::make('custom_fields')
                        ->label('Custom fields')
                        ->keyLabel('Field')
                        ->valueLabel('Value')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Name')
                    ->state(fn (Contact $record): string => $record->full_name)
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['last_name']),
                TextColumn::make('email')->searchable()->placeholder('—'),
                TextColumn::make('account.name')->label('Account')->placeholder('—'),
                TextColumn::make('lifecycle_stage')
                    ->label('Stage')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->headline()->toString())
                    ->color(fn (string $state): string => match ($state) {
                        'customer' => 'success',
                        'opportunity', 'sales_qualified' => 'info',
                        'churned' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('owner.full_name')->label('Owner'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('lifecycle_stage')->label('Stage')->options(self::lifecycleOptions()),
                SelectFilter::make('owner_id')
                    ->label('Owner')
                    ->options(fn (): array => User::query()->get()->mapWithKeys(
                        fn (User $user): array => [$user->id => $user->full_name],
                    )->all()),
                SelectFilter::make('account_id')
                    ->label('Account')
                    ->options(fn (): array => Account::query()->orderBy('name')->pluck('name', 'id')->all()),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('changeStage')
                        ->label('Change stage')
                        ->icon('heroicon-o-arrow-trending-up')
                        ->visible(function (): bool {
                            $user = Auth::user();

                            return $user instanceof User && $user->can('crm.contacts.change-lifecycle');
                        })
                        ->schema([
                            Select::make('stage')->options(self::lifecycleOptions())->required(),
                        ])
                        ->action(function (Contact $record, array $data): void {
                            app(ContactServiceInterface::class)->moveLifecycleStage($record->id, $data['stage']);
                            Notification::make()->success()->title('Stage updated')->send();
                        }),
                    Action::make('merge')
                        ->label('Merge into…')
                        ->icon('heroicon-o-arrows-pointing-in')
                        ->color('warning')
                        ->visible(function (): bool {
                            $user = Auth::user();

                            return $user instanceof User && $user->can('crm.contacts.merge');
                        })
                        ->modalDescription('Everything on the selected duplicate — activities, deals, account links — moves onto this contact; the duplicate is removed.')
                        ->schema([
                            Select::make('merge_id')
                                ->label('Duplicate to merge away')
                                ->options(fn (Contact $record): array => Contact::query()
                                    ->whereKeyNot($record->id)
                                    ->get()
                                    ->sortBy('last_name')
                                    ->mapWithKeys(fn (Contact $contact): array => [
                                        $contact->id => $contact->full_name.($contact->email ? " ({$contact->email})" : ''),
                                    ])->all())
                                ->searchable()
                                ->required(),
                        ])
                        ->requiresConfirmation()
                        ->action(function (Contact $record, array $data): void {
                            try {
                                app(ContactServiceInterface::class)->merge($record->id, $data['merge_id']);
                                Notification::make()->success()->title('Contacts merged')->send();
                            } catch (Throwable $e) {
                                $message = $e instanceof ValidationException
                                    ? collect($e->errors())->flatten()->first()
                                    : $e->getMessage();
                                Notification::make()->danger()->title($message)->send();
                            }
                        }),
                    DeleteAction::make()
                        ->visible(function (): bool {
                            $user = Auth::user();

                            return $user instanceof User && $user->can('crm.contacts.delete');
                        }),
                ]),
            ])
            ->emptyStateHeading('No contacts yet')
            ->emptyStateDescription('Add your first contact — every deal, activity and account hangs off one.');
    }

    /** @return array<string, string> */
    public static function lifecycleOptions(): array
    {
        return collect(Contact::LIFECYCLE_STAGES)->mapWithKeys(
            fn (string $stage): array => [$stage => str($stage)->replace('_', ' ')->headline()->toString()],
        )->all();
    }

    public static function getPages(): array
    {
        return [
            'index' => ContactResource\Pages\ListContacts::route('/'),
            'create' => ContactResource\Pages\CreateContact::route('/create'),
            'edit' => ContactResource\Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
