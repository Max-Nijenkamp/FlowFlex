<?php

namespace App\Filament\Admin\Resources\Marketing;

use App\Filament\Admin\Enums\NavigationGroup;
use App\Filament\Admin\Resources\Marketing\HelpCategoryResource\Pages\CreateHelpCategory;
use App\Filament\Admin\Resources\Marketing\HelpCategoryResource\Pages\EditHelpCategory;
use App\Filament\Admin\Resources\Marketing\HelpCategoryResource\Pages\ListHelpCategories;
use App\Models\Marketing\HelpCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class HelpCategoryResource extends Resource
{
    protected static ?string $model = HelpCategory::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-book-open';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::MarketingContent;

    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::MarketingContent->label();
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.help_categories.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.help_categories.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('admin.resources.help_categories.sections.help_category'))
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, ?string $state, Set $set) =>
                            $operation === 'create' ? $set('slug', Str::slug($state ?? '')) : null
                        ),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Textarea::make('description')
                        ->rows(3)
                        ->columnSpanFull(),

                    TextInput::make('icon')
                        ->maxLength(255)
                        ->helperText('Heroicon name, e.g. heroicon-o-cog'),

                    Select::make('parent_id')
                        ->label('Parent Category')
                        ->options(fn () => HelpCategory::whereNull('parent_id')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->nullable()
                        ->placeholder('None (top-level)'),

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

                TextColumn::make('slug')
                    ->color('gray'),

                TextColumn::make('parent.name')
                    ->label('Parent')
                    ->color('gray')
                    ->placeholder('—'),

                TextColumn::make('articles_count')
                    ->label('Articles')
                    ->counts('articles')
                    ->sortable()
                    ->badge()
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
            'index'  => ListHelpCategories::route('/'),
            'create' => CreateHelpCategory::route('/create'),
            'edit'   => EditHelpCategory::route('/{record}/edit'),
        ];
    }
}
