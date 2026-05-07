<?php

namespace App\Filament\Admin\Resources\Marketing;

use App\Filament\Admin\Enums\NavigationGroup;
use App\Filament\Admin\Resources\Marketing\BlogPostResource\Pages\CreateBlogPost;
use App\Filament\Admin\Resources\Marketing\BlogPostResource\Pages\EditBlogPost;
use App\Filament\Admin\Resources\Marketing\BlogPostResource\Pages\ListBlogPosts;
use App\Models\Marketing\BlogCategory;
use App\Models\Marketing\BlogPost;
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
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::MarketingContent;

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Content')
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

                    Select::make('blog_category_id')
                        ->label('Category')
                        ->options(fn () => BlogCategory::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->required(),

                    TextInput::make('tags')
                        ->helperText('Comma-separated tags (stored as JSON array)')
                        ->dehydrateStateUsing(fn (?string $state): ?array =>
                            $state ? array_map('trim', explode(',', $state)) : null
                        )
                        ->formatStateUsing(fn ($state): string =>
                            is_array($state) ? implode(', ', $state) : ($state ?? '')
                        ),

                    Textarea::make('excerpt')
                        ->rows(3)
                        ->columnSpanFull(),

                    Textarea::make('body')
                        ->required()
                        ->rows(20)
                        ->columnSpanFull(),

                    TextInput::make('author_id')
                        ->label('Author ID')
                        ->default(fn () => Auth::id())
                        ->hidden(),
                ]),

            Section::make('Publishing')
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->options([
                            'draft'     => 'Draft',
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                        ])
                        ->default('draft')
                        ->required(),

                    DateTimePicker::make('published_at')
                        ->label('Publish At'),
                ]),

            Section::make('SEO')
                ->columns(2)
                ->schema([
                    TextInput::make('seo_title')
                        ->label('SEO Title')
                        ->maxLength(255),

                    Textarea::make('seo_description')
                        ->label('SEO Description')
                        ->rows(3),

                    Toggle::make('seo_noindex')
                        ->label('No Index')
                        ->inline(false),

                    FileUpload::make('og_image')
                        ->label('OG Image')
                        ->image()
                        ->nullable(),
                ]),

            Section::make('Settings')
                ->columns(2)
                ->schema([
                    Select::make('cta_type')
                        ->label('CTA Type')
                        ->options([
                            'demo'    => 'Demo',
                            'module'  => 'Module',
                            'pricing' => 'Pricing',
                            'none'    => 'None',
                        ])
                        ->default('demo')
                        ->live(),

                    TextInput::make('cta_module')
                        ->label('CTA Module')
                        ->maxLength(255)
                        ->visible(fn (callable $get): bool => $get('cta_type') === 'module'),
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

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'scheduled' => 'warning',
                        default     => 'gray',
                    }),

                TextColumn::make('author_id')
                    ->label('Author')
                    ->color('gray'),

                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('d M Y')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'scheduled' => 'Scheduled',
                        'published' => 'Published',
                    ]),

                SelectFilter::make('blog_category_id')
                    ->label('Category')
                    ->options(fn () => BlogCategory::pluck('name', 'id')->toArray()),
            ])
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
            'index'  => ListBlogPosts::route('/'),
            'create' => CreateBlogPost::route('/create'),
            'edit'   => EditBlogPost::route('/{record}/edit'),
        ];
    }
}
