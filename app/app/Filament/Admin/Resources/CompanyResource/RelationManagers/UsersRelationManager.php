<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CompanyResource\RelationManagers;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $title = 'Users';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->schema([
                TextInput::make('first_name')
                    ->required()
                    ->maxLength(100),
                TextInput::make('last_name')
                    ->required()
                    ->maxLength(100),
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
                            $this->getOwnerRecord()->id,
                        ),
                    ),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $context) => $context === 'create')
                    ->minLength(8)
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                Select::make('status')
                    ->options([
                        'active'      => 'Active',
                        'invited'     => 'Invited',
                        'deactivated' => 'Deactivated',
                    ])
                    ->required()
                    ->default('active'),
                Select::make('locale')
                    ->options([
                        'en'    => 'English',
                        'nl'    => 'Dutch',
                        'de'    => 'German',
                        'fr'    => 'French',
                        'es'    => 'Spanish',
                    ])
                    ->required()
                    ->default('en'),
            ])->columns(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('email')
            ->columns([
                TextColumn::make('first_name')
                    ->label('First name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('last_name')
                    ->label('Last name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
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
                    ->sortable()
                    ->placeholder('Never'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
