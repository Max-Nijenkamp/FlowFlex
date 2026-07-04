<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Actions\ResendInvitationAction;
use App\Actions\RevokeInvitationAction;
use App\Actions\SendInvitationAction;
use App\Data\CreateInvitationData;
use App\Models\User;
use App\Models\UserInvitation;
use App\Services\BillingService;
use App\Support\Services\CompanyContext;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Throwable;

/**
 * Pending invites list + send/resend/revoke (core.invitation-system/
 * send-invite). Creation happens via the modal action, never a create page.
 */
class InvitationResource extends Resource
{
    protected static ?string $model = UserInvitation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static string|\UnitEnum|null $navigationGroup = 'Team';

    protected static ?string $navigationLabel = 'Invitations';

    protected static ?string $modelLabel = 'invitation';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('core.invitations.view-any')
            && app(BillingService::class)->hasModule('core.invitations');
    }

    public static function canCreate(): bool
    {
        return false; // the header "Invite" modal action owns creation
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')->searchable(),
                TextColumn::make('role')->badge(),
                TextColumn::make('inviter.full_name')->label('Invited by')->default('—'),
                TextColumn::make('status')
                    ->state(fn (UserInvitation $record): string => match (true) {
                        $record->accepted_at !== null => 'Accepted',
                        $record->revoked_at !== null => 'Revoked',
                        $record->expires_at->isPast() => 'Expired',
                        default => 'Pending',
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'info',
                        'Accepted' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Action::make('invite')
                    ->label('Invite')
                    ->icon('heroicon-o-paper-airplane')
                    ->visible(function (): bool {
                        $user = Auth::user();

                        return $user instanceof User && $user->can('core.invitations.send');
                    })
                    ->schema([
                        TextInput::make('email')->email()->required(),
                        Select::make('role')
                            ->options(fn (): array => Role::query()
                                ->where('company_id', app(CompanyContext::class)->currentId())
                                ->where('name', '!=', 'owner')
                                ->pluck('name', 'name')
                                ->all())
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        try {
                            SendInvitationAction::run(new CreateInvitationData(
                                email: $data['email'],
                                role: $data['role'],
                            ));
                            Notification::make()->success()->title('Invitation sent')->send();
                        } catch (ValidationException $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();
                        }
                    }),
            ])
            ->recordActions([
                Action::make('resend')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(function (UserInvitation $record): bool {
                        $user = Auth::user();

                        return $user instanceof User
                            && $user->can('core.invitations.resend')
                            && $record->accepted_at === null
                            && $record->revoked_at === null;
                    })
                    ->action(function (UserInvitation $record): void {
                        try {
                            ResendInvitationAction::run($record->id);
                            Notification::make()->success()->title('Invitation re-sent with a fresh link')->send();
                        } catch (Throwable $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();
                        }
                    }),
                Action::make('revoke')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(function (UserInvitation $record): bool {
                        $user = Auth::user();

                        return $user instanceof User
                            && $user->can('core.invitations.revoke')
                            && $record->accepted_at === null
                            && $record->revoked_at === null;
                    })
                    ->action(function (UserInvitation $record): void {
                        try {
                            RevokeInvitationAction::run($record->id);
                            Notification::make()->success()->title('Invitation revoked')->send();
                        } catch (Throwable $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();
                        }
                    }),
            ])
            ->emptyStateHeading('No invitations yet')
            ->emptyStateDescription('Invite teammates and they will show up here until they join.');
    }

    public static function getPages(): array
    {
        return [
            'index' => InvitationResource\Pages\ListInvitations::route('/'),
        ];
    }
}
