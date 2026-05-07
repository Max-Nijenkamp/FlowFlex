<?php

namespace App\Filament\Admin\Resources\Marketing;

use App\Filament\Admin\Enums\NavigationGroup;
use App\Filament\Admin\Resources\Marketing\TeamMemberResource\Pages\CreateTeamMember;
use App\Filament\Admin\Resources\Marketing\TeamMemberResource\Pages\EditTeamMember;
use App\Filament\Admin\Resources\Marketing\TeamMemberResource\Pages\ListTeamMembers;
use App\Models\Marketing\TeamMember;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamMemberResource extends Resource
{
    protected static ?string $model = TeamMember::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::MarketingContent;

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Team Member')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('role')
                        ->required()
                        ->maxLength(255),

                    Textarea::make('bio')
                        ->rows(4)
                        ->maxLength(300)
                        ->columnSpanFull(),

                    FileUpload::make('photo')
                        ->image()
                        ->nullable(),

                    TextInput::make('linkedin_url')
                        ->url()
                        ->maxLength(255),

                    TextInput::make('twitter_url')
                        ->url()
                        ->maxLength(255),

                    TextInput::make('display_order')
                        ->numeric()
                        ->default(0),

                    Toggle::make('is_published')
                        ->default(true)
                        ->inline(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->color('gray'),

                TextColumn::make('display_order')
                    ->label('Order')
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('display_order')
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTeamMembers::route('/'),
            'create' => CreateTeamMember::route('/create'),
            'edit'   => EditTeamMember::route('/{record}/edit'),
        ];
    }
}
