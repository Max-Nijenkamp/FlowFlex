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
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ManageApiKeys extends Page implements HasTable
{
    use InteractsWithTable;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'API Keys';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 30;

    protected string $view = 'filament.workspace.pages.settings.manage-api-keys';

    /** Holds the plaintext key temporarily after creation so the view can display it. */
    public ?string $newlyCreatedKey = null;

    public function mount(): void
    {
        abort_unless(
            auth('tenant')->check(),
            403
        );
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ApiKey::query()
                    ->where('company_id', auth('tenant')->user()->company_id)
                    ->withoutGlobalScopes()
            )
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Medium),

                TextColumn::make('key_prefix')
                    ->label('Key prefix')
                    ->formatStateUsing(fn (string $state) => $state . str_repeat('*', 28))
                    ->fontFamily(FontFamily::Mono)
                    ->color('gray'),

                TextColumn::make('scopes')
                    ->label('Scopes')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return 'All modules';
                        }
                        return is_array($state) ? implode(', ', $state) : $state;
                    })
                    ->badge()
                    ->color('primary'),

                TextColumn::make('last_used_at')
                    ->label('Last used')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Never')
                    ->color('gray'),

                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('Never')
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                TableAction::make('create_key')
                    ->label('Create API key')
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
                                ->title('API key created — copy it now')
                                ->body('This is the only time the key will be shown. Store it somewhere safe.')
                                ->persistent()
                                ->send();
                        }
                    }),
            ])
            ->actions([
                TableAction::make('revoke')
                    ->label('Revoke')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Revoke API key')
                    ->modalDescription('This will immediately invalidate the key. This cannot be undone.')
                    ->action(function (ApiKey $record): void {
                        $this->authorizeEdit();
                        $record->delete();

                        Notification::make()
                            ->success()
                            ->title('API key revoked')
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
                    ->label('Key name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Production Integration'),

                Select::make('scopes')
                    ->label('Allowed modules (scopes)')
                    ->multiple()
                    ->options($moduleOptions)
                    ->helperText('Leave empty to allow access to all active modules.')
                    ->searchable(),

                DateTimePicker::make('expires_at')
                    ->label('Expiry date')
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
