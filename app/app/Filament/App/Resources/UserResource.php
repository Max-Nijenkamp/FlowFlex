<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Actions\AssignRolesAction;
use App\Actions\TransferOwnershipAction;
use App\Data\AssignRolesData;
use App\Models\User;
use App\Support\Services\CompanyContext;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Throwable;

/**
 * Team members list + role assignment + ownership transfer (core.rbac).
 * Read-only rows — user creation arrives with core.invitation-system.
 */
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Team';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Members';

    protected static ?string $modelLabel = 'member';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->can('core.rbac.view-any');
    }

    public static function canCreate(): bool
    {
        return false; // members join via invitations
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->label('Name')->searchable(['first_name', 'last_name']),
                TextColumn::make('email')->searchable(),
                TextColumn::make('roles.name')->label('Roles')->badge(),
                TextColumn::make('created_at')->label('Member since')->date('d M Y')->sortable(),
            ])
            ->recordActions([
                Action::make('editMember')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->visible(function (): bool {
                        $user = Auth::user();

                        return $user instanceof User && $user->can('core.rbac.assign-roles');
                    })
                    ->schema([
                        TextInput::make('first_name')->required()->maxLength(255)
                            ->default(fn (User $record): string => $record->first_name),
                        TextInput::make('last_name')->required()->maxLength(255)
                            ->default(fn (User $record): string => $record->last_name),
                        TextInput::make('email')->email()->required()->maxLength(255)
                            ->default(fn (User $record): string => $record->email),
                    ])
                    ->action(function (User $record, array $data): void {
                        validator($data, [
                            'email' => [Rule::unique('users', 'email')->ignore($record->id)],
                        ])->validate();

                        $record->update([
                            'first_name' => $data['first_name'],
                            'last_name' => $data['last_name'],
                            'email' => $data['email'],
                        ]);

                        Notification::make()->success()->title('Member updated')->send();
                    }),
                Action::make('assignRoles')
                    ->label('Roles')
                    ->icon('heroicon-o-shield-check')
                    ->visible(function (): bool {
                        $user = Auth::user();

                        return $user instanceof User && $user->can('core.rbac.assign-roles');
                    })
                    ->schema([
                        CheckboxList::make('roles')
                            ->label('Roles')
                            ->options(fn (): array => Role::query()
                                ->where('company_id', app(CompanyContext::class)->currentId())
                                ->where('name', '!=', 'owner') // ownership moves only via transfer
                                ->pluck('name', 'name')
                                ->all())
                            ->default(fn (User $record): array => $record->getRoleNames()->reject(fn (string $n): bool => $n === 'owner')->values()->all())
                            ->bulkToggleable(),
                    ])
                    ->action(function (User $record, array $data): void {
                        try {
                            $roles = $data['roles'] ?? [];

                            if ($record->hasRole('owner')) {
                                $roles[] = 'owner'; // never silently strip ownership
                            }

                            AssignRolesAction::run(new AssignRolesData(userId: $record->id, roles: array_values($roles)));
                            Notification::make()->success()->title('Roles updated')->send();
                        } catch (Throwable $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();
                        }
                    }),
                Action::make('transferOwnership')
                    ->label('Make owner')
                    ->icon('heroicon-o-key')
                    ->color('danger')
                    ->visible(fn (User $record): bool => Auth::user() instanceof User
                        && Auth::user()->hasRole('owner')
                        && Auth::user()->can('core.rbac.transfer-ownership')
                        && ! $record->hasRole('owner'))
                    ->schema([
                        Select::make('confirm_member')
                            ->label('New owner')
                            ->options(fn (User $record): array => [$record->id => $record->full_name])
                            ->default(fn (User $record): string => $record->id)
                            ->disabled(),
                        TextInput::make('company_name_confirmation')
                            ->label(fn (): string => 'Type "'.app(CompanyContext::class)->current()->name.'" to confirm')
                            ->required(),
                    ])
                    ->modalHeading('Transfer ownership')
                    ->modalDescription('You will be demoted to admin. This cannot be undone without the new owner transferring back.')
                    ->action(function (User $record, array $data): void {
                        if (($data['company_name_confirmation'] ?? '') !== app(CompanyContext::class)->current()->name) {
                            throw ValidationException::withMessages([
                                'company_name_confirmation' => 'The company name does not match.',
                            ]);
                        }

                        try {
                            TransferOwnershipAction::run($record->id);
                            Notification::make()->success()->title('Ownership transferred')->send();
                        } catch (Throwable $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();
                        }
                    }),
            ])
            ->emptyStateHeading('No members yet')
            ->emptyStateDescription('Invite teammates to start collaborating.');
    }

    public static function getPages(): array
    {
        return [
            'index' => UserResource\Pages\ListUsers::route('/'),
        ];
    }
}
