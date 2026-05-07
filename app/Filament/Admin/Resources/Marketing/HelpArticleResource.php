<?php

namespace App\Filament\Admin\Resources\Marketing;

use App\Filament\Admin\Enums\NavigationGroup;
use App\Filament\Admin\Resources\Marketing\HelpArticleResource\Pages\CreateHelpArticle;
use App\Filament\Admin\Resources\Marketing\HelpArticleResource\Pages\EditHelpArticle;
use App\Filament\Admin\Resources\Marketing\HelpArticleResource\Pages\ListHelpArticles;
use App\Models\Marketing\HelpArticle;
use App\Models\Marketing\HelpCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
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

class HelpArticleResource extends Resource
{
    protected static ?string $model = HelpArticle::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::MarketingContent;

    protected static ?int $navigationSort = 11;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::MarketingContent->label();
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.help_articles.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.help_articles.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('admin.resources.help_articles.sections.content'))
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

                    Select::make('help_category_id')
                        ->label('Category')
                        ->options(fn () => HelpCategory::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->required(),

                    Textarea::make('body')
                        ->required()
                        ->rows(20)
                        ->columnSpanFull(),
                ]),

            Section::make(__('admin.resources.help_articles.sections.seo_publishing'))
                ->columns(2)
                ->schema([
                    TextInput::make('seo_title')
                        ->label(__('admin.resources.help_articles.fields.seo_title'))
                        ->maxLength(255),

                    Textarea::make('seo_description')
                        ->label(__('admin.resources.help_articles.fields.seo_description'))
                        ->rows(3),

                    Toggle::make('is_published')
                        ->label(__('admin.resources.help_articles.fields.is_published'))
                        ->default(false)
                        ->inline(false),

                    DateTimePicker::make('last_reviewed_at')
                        ->label(__('admin.resources.help_articles.fields.last_reviewed')),
                ]),

            Section::make(__('admin.resources.help_articles.sections.platform'))
                ->schema([
                    TextInput::make('module_link')
                        ->label(__('admin.resources.help_articles.fields.module_link'))
                        ->maxLength(255)
                        ->nullable()
                        ->helperText('Link to a related FlowFlex module slug'),
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

                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('last_reviewed_at')
                    ->label('Last Reviewed')
                    ->dateTime('d M Y')
                    ->sortable(),

                TextColumn::make('helpful_count')
                    ->label('Helpful')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('not_helpful_count')
                    ->label('Not Helpful')
                    ->sortable()
                    ->badge()
                    ->color('danger'),
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
            'index'  => ListHelpArticles::route('/'),
            'create' => CreateHelpArticle::route('/create'),
            'edit'   => EditHelpArticle::route('/{record}/edit'),
        ];
    }
}
