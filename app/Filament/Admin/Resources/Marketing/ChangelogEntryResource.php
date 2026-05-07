<?php

namespace App\Filament\Admin\Resources\Marketing;

use App\Filament\Admin\Enums\NavigationGroup;
use App\Filament\Admin\Resources\Marketing\ChangelogEntryResource\Pages\CreateChangelogEntry;
use App\Filament\Admin\Resources\Marketing\ChangelogEntryResource\Pages\EditChangelogEntry;
use App\Filament\Admin\Resources\Marketing\ChangelogEntryResource\Pages\ListChangelogEntries;
use App\Models\Marketing\ChangelogEntry;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChangelogEntryResource extends Resource
{
    protected static ?string $model = ChangelogEntry::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-clock';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::MarketingContent;

    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Changelog Entry')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Select::make('type')
                        ->options([
                            'feature'        => 'Feature',
                            'improvement'    => 'Improvement',
                            'fix'            => 'Fix',
                            'infrastructure' => 'Infrastructure',
                        ])
                        ->required(),

                    DateTimePicker::make('published_at')
                        ->label('Publish At'),

                    Toggle::make('is_published')
                        ->label('Published')
                        ->default(false)
                        ->inline(false),

                    Textarea::make('body')
                        ->required()
                        ->rows(10)
                        ->columnSpanFull(),

                    FileUpload::make('screenshot')
                        ->image()
                        ->nullable(),

                    TextInput::make('docs_url')
                        ->url()
                        ->maxLength(255)
                        ->nullable(),
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

                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'feature'        => 'success',
                        'improvement'    => 'info',
                        'fix'            => 'warning',
                        'infrastructure' => 'gray',
                        default          => 'gray',
                    }),

                TextColumn::make('published_at')
                    ->label('Publish At')
                    ->dateTime('d M Y')
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
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
            'index'  => ListChangelogEntries::route('/'),
            'create' => CreateChangelogEntry::route('/create'),
            'edit'   => EditChangelogEntry::route('/{record}/edit'),
        ];
    }
}
