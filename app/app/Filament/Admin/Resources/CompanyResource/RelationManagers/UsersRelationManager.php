<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CompanyResource\RelationManagers;

use App\Actions\SendInvitationAction;
use App\Data\CreateInvitationData;
use App\Filament\Admin\Concerns\RunsInCompanyContext;
use App\Models\Company;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UsersRelationManager extends RelationManager
{
    use RunsInCompanyContext;

    protected static string $relationship = 'users';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->latest('last_login_at'))
            ->columns([
                TextColumn::make('full_name')->label('Name'),
                TextColumn::make('email')->searchable(),
                TextColumn::make('roles')->label('Roles')->badge()
                    ->state(function (User $r): array {
                        /** @var Company $company */
                        $company = $this->getOwnerRecord();

                        return $this->withCompanyContext($company, fn (): array => $r->getRoleNames()->all());
                    }),
                IconColumn::make('email_verified_at')->label('Verified')
                    ->boolean()
                    ->state(fn (User $r): bool => $r->email_verified_at !== null),
                TextColumn::make('last_login_at')->dateTime()->sortable(),
            ])
            ->headerActions([
                Action::make('invite')
                    ->label('Invite user')
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->schema([
                        TextInput::make('email')->email()->required(),
                        Select::make('role')
                            ->options(function (): array {
                                /** @var Company $company */
                                $company = $this->getOwnerRecord();

                                return Role::query()
                                    ->where('team_id', $company->id)
                                    ->pluck('name', 'name')
                                    ->all();
                            })
                            ->default('owner')
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        /** @var Company $company */
                        $company = $this->getOwnerRecord();

                        try {
                            $this->withCompanyContext($company, fn () => SendInvitationAction::run(
                                new CreateInvitationData(email: (string) $data['email'], role: (string) $data['role']),
                            ));
                            Notification::make()->success()->title('Invitation sent')->send();
                        } catch (ValidationException $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();
                        }
                    }),
            ])
            ->recordActions([
                Action::make('makeOwner')
                    ->label('Make owner')
                    ->icon(Heroicon::OutlinedStar)
                    ->requiresConfirmation()
                    ->modalDescription('Owners can change company settings and active modules.')
                    ->visible(function (User $record): bool {
                        /** @var Company $company */
                        $company = $this->getOwnerRecord();

                        return $this->withCompanyContext($company, fn (): bool => ! $record->hasRole('owner'));
                    })
                    ->action(function (User $record): void {
                        /** @var Company $company */
                        $company = $this->getOwnerRecord();

                        $this->withCompanyContext($company, function () use ($record): void {
                            $record->assignRole('owner');
                        });

                        Notification::make()->success()->title('Owner role assigned')->send();
                    }),
            ]);
    }
}
