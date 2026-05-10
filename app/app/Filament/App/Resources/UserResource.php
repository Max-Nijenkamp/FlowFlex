<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\UserResource\Pages\CreateUser;
use App\Filament\App\Resources\UserResource\Pages\EditUser;
use App\Filament\App\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use App\Support\Services\CompanyContext;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-users';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function getNavigationLabel(): string
    {
        return 'Users & Roles';
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('User Details')->columnSpanFull()->schema([
                TextInput::make('first_name')
                    ->required()
                    ->maxLength(100)
                    ->label('First name'),
                TextInput::make('last_name')
                    ->required()
                    ->maxLength(100)
                    ->label('Last name'),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(
                        table: User::class,
                        column: 'email',
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule) => $rule->where(
                            'company_id',
                            app(CompanyContext::class)->current()->id,
                        ),
                    ),
                Select::make('locale')
                    ->options([
                        'en'    => 'English',
                        'nl'    => 'Dutch',
                        'de'    => 'German',
                        'fr'    => 'French',
                        'nl-NL' => 'Dutch (Netherlands)',
                        'en-GB' => 'English (UK)',
                    ])
                    ->default('en'),
                Select::make('timezone')
                    ->options(fn () => array_combine(
                        \DateTimeZone::listIdentifiers(),
                        \DateTimeZone::listIdentifiers(),
                    ))
                    ->searchable()
                    ->default('UTC'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->label('Name')
                    ->formatStateUsing(fn (User $record) => "{$record->first_name} {$record->last_name}")
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'      => 'success',
                        'invited'     => 'warning',
                        'deactivated' => 'gray',
                        default       => 'gray',
                    }),
                TextColumn::make('last_login_at')
                    ->dateTime()
                    ->label('Last login')
                    ->placeholder('Never'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active'      => 'Active',
                        'invited'     => 'Invited',
                        'deactivated' => 'Deactivated',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                Action::make('resend_invite')
                    ->label('Resend invite')
                    ->icon('heroicon-o-envelope')
                    ->visible(fn (User $record) => $record->status === 'invited')
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $token = \Illuminate\Support\Str::random(64);
                        \App\Models\UserInvitation::updateOrCreate(
                            ['user_id' => $record->id],
                            [
                                'company_id'  => $record->company_id,
                                'token'       => $token,
                                'expires_at'  => now()->addDays(7),
                                'accepted_at' => null,
                            ]
                        );
                        event(new \App\Events\Foundation\UserInvited($record, $record->company, $token));
                        Notification::make()
                            ->title('Invite resent')
                            ->success()
                            ->send();
                    }),
                Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (User $record) => $record->status === 'active')
                    ->action(function (User $record): void {
                        $record->update(['status' => 'deactivated']);
                        Notification::make()
                            ->title('User deactivated')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit'   => EditUser::route('/{record}/edit'),
        ];
    }
}
