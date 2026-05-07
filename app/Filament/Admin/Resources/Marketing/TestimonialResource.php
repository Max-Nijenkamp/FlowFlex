<?php

namespace App\Filament\Admin\Resources\Marketing;

use App\Filament\Admin\Enums\NavigationGroup;
use App\Filament\Admin\Resources\Marketing\TestimonialResource\Pages\CreateTestimonial;
use App\Filament\Admin\Resources\Marketing\TestimonialResource\Pages\EditTestimonial;
use App\Filament\Admin\Resources\Marketing\TestimonialResource\Pages\ListTestimonials;
use App\Models\Marketing\Testimonial;
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

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::MarketingContent;

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Testimonial Details')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('role')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('company')
                        ->required()
                        ->maxLength(255),

                    FileUpload::make('photo')
                        ->image()
                        ->nullable(),

                    Textarea::make('quote')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),

                    Toggle::make('is_featured')
                        ->label('Featured')
                        ->inline(false),

                    TextInput::make('display_order')
                        ->numeric()
                        ->default(0),

                    Toggle::make('is_published')
                        ->label('Published')
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

                TextColumn::make('company')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->sortable(),

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
            'index'  => ListTestimonials::route('/'),
            'create' => CreateTestimonial::route('/create'),
            'edit'   => EditTestimonial::route('/{record}/edit'),
        ];
    }
}
