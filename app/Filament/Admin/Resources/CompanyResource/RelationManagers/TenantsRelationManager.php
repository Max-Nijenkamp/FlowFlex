<?php

namespace App\Filament\Admin\Resources\CompanyResource\RelationManagers;

use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TenantsRelationManager extends RelationManager
{
    protected static string $relationship = 'tenants';

    protected static ?string $title = 'Users';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Name')
                ->columns(3)
                ->schema([
                    TextInput::make('first_name')->required()->maxLength(255),
                    TextInput::make('middle_name')->maxLength(255),
                    TextInput::make('last_name')->required()->maxLength(255),
                ]),

            Section::make('Contact')
                ->columns(2)
                ->schema([
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(table: 'tenants', ignoreRecord: true)
                        ->maxLength(255),
                    TextInput::make('phone')->tel()->maxLength(50),
                ]),

            Section::make('Password')
                ->schema([
                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->required(fn (string $operation) => $operation === 'create')
                        ->dehydrated(fn (?string $state) => filled($state))
                        ->maxLength(255),
                ]),

            Section::make('Status')
                ->schema([
                    Toggle::make('is_enabled')->label('User active')->default(true),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Name')
                    ->getStateUsing(fn (Tenant $record) => $record->fullName())
                    ->searchable(['first_name', 'last_name'])
                    ->weight(FontWeight::Medium),

                TextColumn::make('email')
                    ->searchable()
                    ->color('gray'),

                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color('gray')
                    ->separator(','),

                IconColumn::make('is_enabled')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('d M Y')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->striped()
            ->filters([
                TernaryFilter::make('is_enabled')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                Action::make('impersonate')
                    ->label('Impersonate')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Tenant $record) => "Impersonate {$record->fullName()}?")
                    ->modalDescription('You will be logged in as this user in the workspace panel. Your admin session remains active.')
                    ->action(function (Tenant $record) {
                        session([
                            'impersonating_user_id'   => Auth::id(),
                            'impersonating_user_name' => Auth::user()->name,
                        ]);

                        Auth::guard('tenant')->login($record);

                        return redirect('/workspace');
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
