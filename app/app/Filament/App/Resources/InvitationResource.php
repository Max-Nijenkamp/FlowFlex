<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Actions\ResendInvitationAction;
use App\Actions\RevokeInvitationAction;
use App\Actions\SendInvitationAction;
use App\Contracts\BillingServiceInterface;
use App\Data\CreateInvitationData;
use App\Models\UserInvitation;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use UnitEnum;

class InvitationResource extends Resource
{
    protected static ?string $model = UserInvitation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = 'Team';

    protected static ?string $modelLabel = 'invitation';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('core.invitations.view-any')
            && app(BillingServiceInterface::class)->hasModule('core.invitations');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('email')->email()->required(),
            Select::make('role')
                ->options(fn () => Role::query()
                    ->where('team_id', getPermissionsTeamId())
                    ->where('name', '!=', 'owner')
                    ->pluck('name', 'name'))
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('email')->searchable(),
                TextColumn::make('role')->badge(),
                TextColumn::make('expires_at')->dateTime()->sortable()
                    ->description(fn (UserInvitation $r) => $r->isUsable() ? $r->expires_at->diffForHumans() : null),
                TextColumn::make('status')
                    ->state(fn (UserInvitation $r): string => match (true) {
                        $r->accepted_at !== null => 'accepted',
                        $r->revoked_at !== null => 'revoked',
                        $r->expires_at->isPast() => 'expired',
                        default => 'pending',
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'accepted' => 'success',
                        'pending' => 'info',
                        default => 'danger',
                    }),
            ])
            ->recordActions([
                Action::make('resend')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->visible(fn (UserInvitation $r) => $r->accepted_at === null
                        && Auth::guard('web')->user()->can('core.invitations.resend'))
                    ->action(function (UserInvitation $record): void {
                        ResendInvitationAction::run($record->id);
                        Notification::make()->success()->title('Invitation re-sent')->send();
                    }),
                Action::make('revoke')
                    ->icon(Heroicon::OutlinedXMark)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (UserInvitation $r) => $r->isUsable()
                        && Auth::guard('web')->user()->can('core.invitations.revoke'))
                    ->action(function (UserInvitation $record): void {
                        RevokeInvitationAction::run($record->id);
                        Notification::make()->success()->title('Invitation revoked')->send();
                    }),
            ])
            ->headerActions([
                Action::make('invite')
                    ->label('Invite member')
                    ->visible(fn () => Auth::guard('web')->user()->can('core.invitations.create'))
                    ->schema([
                        TextInput::make('email')->email()->required(),
                        Select::make('role')
                            ->options(fn () => Role::query()
                                ->where('team_id', getPermissionsTeamId())
                                ->where('name', '!=', 'owner')
                                ->pluck('name', 'name'))
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        try {
                            SendInvitationAction::run(CreateInvitationData::from($data));
                            Notification::make()->success()->title('Invitation sent')->send();
                        } catch (ValidationException $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => InvitationResource\Pages\ListInvitations::route('/'),
        ];
    }
}
