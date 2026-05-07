<?php

namespace App\Filament\Workspace\Pages\Settings;

use App\Models\ApiKey;
use App\Models\Module;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Actions\Action as TableAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ManageApiKeys extends Page implements HasTable
{
    use InteractsWithTable;


    protected static \UnitEnum|string|null $navigationGroup = 'Settings';

    public static function getNavigationLabel(): string
    {
        return __('workspace.pages.api_keys.nav_label');
    }

    protected static ?int $navigationSort = 30;

    protected string $view = 'filament.workspace.pages.settings.manage-api-keys';

    /** Holds the plaintext key temporarily after creation so the view can display it. */
    public ?string $newlyCreatedKey = null;

    public function mount(): void
    {
        abort_unless(
            auth('tenant')->user()?->can('workspace.settings.edit'),
            403
        );
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // BelongsToCompany global scope auto-applies company_id for the tenant guard.
                // withoutTrashed() explicitly enforces the SoftDeletes scope without bypassing
                // global scopes or relying on manual whereNull('deleted_at').
                ApiKey::query()->withoutTrashed()
            )
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Medium),

                TextColumn::make('key_prefix')
                    ->label(__('workspace.pages.api_keys.fields.key_prefix'))
                    ->formatStateUsing(fn (string $state) => $state . str_repeat('*', 28))
                    ->fontFamily(FontFamily::Mono)
                    ->color('gray'),

                TextColumn::make('scopes')
                    ->label(__('workspace.pages.api_keys.fields.scopes'))
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return 'All modules';
                        }
                        return is_array($state) ? implode(', ', $state) : $state;
                    })
                    ->badge()
                    ->color('primary'),

                TextColumn::make('last_used_at')
                    ->label(__('workspace.pages.api_keys.fields.last_used'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Never')
                    ->color('gray'),

                TextColumn::make('expires_at')
                    ->label(__('workspace.pages.api_keys.fields.expires'))
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('Never')
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label(__('workspace.pages.api_keys.fields.created'))
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                TableAction::make('create_key')
                    ->label(__('workspace.pages.api_keys.actions.create_key'))
                    ->icon('heroicon-o-plus')
                    ->form(fn (Schema $schema) => $this->createKeyForm($schema))
                    ->action(function (array $data): void {
                        $this->authorizeEdit();

                        $generated = ApiKey::generateKey();

                        ApiKey::create([
                            'company_id'             => auth('tenant')->user()->company_id,
                            'created_by_tenant_id'   => auth('tenant')->id(),
                            'name'                   => $data['name'],
                            'key_hash'               => $generated['hash'],
                            'key_prefix'             => $generated['prefix'],
                            'scopes'                 => ! empty($data['scopes']) ? $data['scopes'] : null,
                            'expires_at'             => ! empty($data['expires_at']) ? $data['expires_at'] : null,
                        ]);

                        $this->newlyCreatedKey = $generated['key'];

                        $this->dispatch('api-key-created');
                    })
                    ->after(function () {
                        if ($this->newlyCreatedKey) {
                            Notification::make()
                                ->success()
                                ->title(__('workspace.pages.api_keys.notifications.created'))
                                ->body(__('workspace.pages.api_keys.notifications.created_body'))
                                ->persistent()
                                ->send();
                        }
                    }),
            ])
            ->actions([
                TableAction::make('revoke')
                    ->label(__('workspace.pages.api_keys.actions.revoke'))
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('workspace.pages.api_keys.modals.revoke_heading'))
                    ->modalDescription(__('workspace.pages.api_keys.modals.revoke_description'))
                    ->action(function (ApiKey $record): void {
                        $this->authorizeEdit();
                        $record->delete();

                        Notification::make()
                            ->success()
                            ->title(__('workspace.pages.api_keys.notifications.revoked'))
                            ->send();
                    }),
            ])
            ->striped()
            ->paginated([10, 25]);
    }

    private function createKeyForm(Schema $schema): Schema
    {
        $moduleOptions = Module::where('is_available', true)
            ->orderBy('name')
            ->pluck('name', 'key')
            ->toArray();

        return $schema->components([
            Section::make()->schema([
                TextInput::make('name')
                    ->label(__('workspace.pages.api_keys.fields.name'))
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Production Integration'),

                Select::make('scopes')
                    ->label(__('workspace.pages.api_keys.fields.scopes'))
                    ->multiple()
                    ->options($moduleOptions)
                    ->helperText('Leave empty to allow access to all active modules.')
                    ->searchable(),

                DateTimePicker::make('expires_at')
                    ->label(__('workspace.pages.api_keys.fields.expires_at'))
                    ->helperText('Leave empty for a key that never expires.')
                    ->native(false)
                    ->minDate(now()),
            ]),
        ]);
    }

    private function authorizeEdit(): void
    {
        abort_unless(
            auth('tenant')->user()?->can('workspace.settings.edit'),
            403
        );
    }
}
