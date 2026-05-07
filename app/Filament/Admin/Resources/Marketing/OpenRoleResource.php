<?php

namespace App\Filament\Admin\Resources\Marketing;

use App\Filament\Admin\Enums\NavigationGroup;
use App\Filament\Admin\Resources\Marketing\OpenRoleResource\Pages\CreateOpenRole;
use App\Filament\Admin\Resources\Marketing\OpenRoleResource\Pages\EditOpenRole;
use App\Filament\Admin\Resources\Marketing\OpenRoleResource\Pages\ListOpenRoles;
use App\Models\Marketing\OpenRole;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class OpenRoleResource extends Resource
{
    protected static ?string $model = OpenRole::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-briefcase';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::MarketingContent;

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Role Details')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, ?string $state, Set $set) =>
                            $operation === 'create' ? $set('slug', Str::slug($state ?? '')) : null
                        )
                        ->columnSpanFull(),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->columnSpanFull(),

                    TextInput::make('department')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('location')
                        ->required()
                        ->maxLength(255),

                    Select::make('type')
                        ->options([
                            'full-time' => 'Full-time',
                            'part-time' => 'Part-time',
                            'contract'  => 'Contract',
                        ])
                        ->required(),

                    TextInput::make('salary_range')
                        ->maxLength(255)
                        ->nullable(),

                    Select::make('status')
                        ->options([
                            'open'   => 'Open',
                            'closed' => 'Closed',
                            'filled' => 'Filled',
                        ])
                        ->required()
                        ->default('open'),

                    DateTimePicker::make('published_at')
                        ->label('Publish At'),
                ]),

            Section::make('Job Description')
                ->schema([
                    Textarea::make('about_role')
                        ->required()
                        ->rows(4),

                    Textarea::make('responsibilities')
                        ->required()
                        ->rows(6),

                    Textarea::make('requirements')
                        ->required()
                        ->rows(6),

                    Textarea::make('nice_to_have')
                        ->rows(4)
                        ->nullable(),

                    Textarea::make('how_to_apply')
                        ->required()
                        ->rows(4),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('department')
                    ->sortable(),

                TextColumn::make('location')
                    ->color('gray'),

                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'full-time' => 'primary',
                        'part-time' => 'info',
                        'contract'  => 'warning',
                        default     => 'gray',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open'   => 'success',
                        'closed' => 'warning',
                        'filled' => 'gray',
                        default  => 'gray',
                    }),

                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index'  => ListOpenRoles::route('/'),
            'create' => CreateOpenRole::route('/create'),
            'edit'   => EditOpenRole::route('/{record}/edit'),
        ];
    }
}
