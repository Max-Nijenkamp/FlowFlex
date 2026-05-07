<?php

namespace App\Filament\Workspace\Pages\Settings;

use App\Models\Role;
use App\Models\Tenant;
use App\Notifications\WorkspaceInviteNotification;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Actions\Action as TableAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ManageTeam extends Page implements HasTable
{
    use InteractsWithTable;


    protected static ?string $navigationLabel = 'Team';

    protected static \UnitEnum|string|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 15;

    protected string $view = 'filament.workspace.pages.settings.manage-team';

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
                Tenant::query()
                    ->withoutGlobalScopes()
                    ->where('company_id', auth('tenant')->user()->company_id)
                    ->whereNull('deleted_at')
            )
            ->columns([
                TextColumn::make('full_name')
                    ->label('Name')
                    ->getStateUsing(fn (Tenant $record) => $record->fullName())
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['last_name'])
                    ->weight(FontWeight::Medium),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->color('gray'),

                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color('primary')
                    ->separator(','),

                IconColumn::make('is_enabled')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('last_name')
            ->actions([
                EditAction::make()
                    ->form(fn (Schema $schema) => $this->memberForm($schema))
                    ->using(function (Tenant $record, array $data): Tenant {
                        $this->authorizeEdit();

                        $updateData = [
                            'first_name' => $data['first_name'],
                            'last_name'  => $data['last_name'],
                            'email'      => $data['email'],
                            'is_enabled' => $data['is_enabled'],
                        ];

                        if (! empty($data['password'])) {
                            $updateData['password'] = $data['password'];
                        }

                        $record->update($updateData);

                        if (! empty($data['roles'])) {
                            $record->syncRoles($data['roles']);
                        }

                        return $record;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Team member updated')
                    ),

                TableAction::make('toggle_enabled')
                    ->label(fn (Tenant $record) => $record->is_enabled ? 'Disable' : 'Enable')
                    ->icon(fn (Tenant $record) => $record->is_enabled ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Tenant $record) => $record->is_enabled ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->action(function (Tenant $record): void {
                        $this->authorizeEdit();

                        $currentTenant = auth('tenant')->user();

                        if ($record->id === $currentTenant->id) {
                            Notification::make()
                                ->warning()
                                ->title('You cannot disable your own account')
                                ->send();
                            return;
                        }

                        $record->update(['is_enabled' => ! $record->is_enabled]);

                        Notification::make()
                            ->success()
                            ->title($record->is_enabled ? 'Member enabled' : 'Member disabled')
                            ->send();
                    }),
            ])
            ->headerActions([
                TableAction::make('add_member')
                    ->label('Add member')
                    ->icon('heroicon-o-user-plus')
                    ->form(fn (Schema $schema) => $this->newMemberForm($schema))
                    ->action(function (array $data): void {
                        $this->authorizeEdit();

                        $plainPassword = ! empty($data['password'])
                            ? $data['password']
                            : Str::password(16);

                        $tenant = Tenant::create([
                            'company_id' => auth('tenant')->user()->company_id,
                            'first_name' => $data['first_name'],
                            'last_name'  => $data['last_name'],
                            'email'      => $data['email'],
                            'password'   => $plainPassword,
                            'is_enabled' => $data['is_enabled'] ?? true,
                        ]);

                        if (! empty($data['roles'])) {
                            $tenant->syncRoles($data['roles']);
                        }

                        $tenant->notify(new WorkspaceInviteNotification(
                            $plainPassword,
                            auth('tenant')->user()->company->name,
                        ));

                        Notification::make()
                            ->success()
                            ->title('Team member added')
                            ->send();
                    }),
            ])
            ->striped()
            ->paginated([10, 25, 50]);
    }

    private function memberForm(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Personal details')
                ->columns(2)
                ->schema([
                    TextInput::make('first_name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('last_name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(Tenant::class, 'email', ignoreRecord: true)
                        ->maxLength(255),

                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn (?string $state) => filled($state))
                        ->helperText('Leave blank to keep the current password.')
                        ->maxLength(255),

                    Select::make('roles')
                        ->label('Roles')
                        ->multiple()
                        ->options(fn () => Role::where('guard_name', 'tenant')->pluck('name', 'name')->toArray())
                        ->preload()
                        ->getStateUsing(fn (Tenant $record) => $record->roles->pluck('name')->toArray()),

                    Toggle::make('is_enabled')
                        ->label('Active')
                        ->default(true)
                        ->inline(false),
                ]),
        ]);
    }

    private function newMemberForm(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Personal details')
                ->columns(2)
                ->schema([
                    TextInput::make('first_name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('last_name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(Tenant::class, 'email')
                        ->maxLength(255),

                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->helperText('Leave blank to auto-generate a secure password.')
                        ->maxLength(255),

                    Select::make('roles')
                        ->label('Roles')
                        ->multiple()
                        ->options(fn () => Role::where('guard_name', 'tenant')->pluck('name', 'name')->toArray())
                        ->preload(),

                    Toggle::make('is_enabled')
                        ->label('Active')
                        ->default(true)
                        ->inline(false),
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
