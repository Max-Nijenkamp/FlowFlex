<?php

namespace App\Filament\Admin\Resources\Marketing;

use App\Filament\Admin\Enums\NavigationGroup;
use App\Filament\Admin\Resources\Marketing\FaqEntryResource\Pages\CreateFaqEntry;
use App\Filament\Admin\Resources\Marketing\FaqEntryResource\Pages\EditFaqEntry;
use App\Filament\Admin\Resources\Marketing\FaqEntryResource\Pages\ListFaqEntries;
use App\Models\Marketing\FaqEntry;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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

class FaqEntryResource extends Resource
{
    protected static ?string $model = FaqEntry::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::MarketingContent;

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('FAQ Entry')
                ->schema([
                    Textarea::make('question')
                        ->required()
                        ->rows(3),

                    Textarea::make('answer')
                        ->required()
                        ->rows(6),

                    Select::make('context')
                        ->options([
                            'general'        => 'General',
                            'pricing'        => 'Pricing',
                            'hr'             => 'HR',
                            'projects'       => 'Projects',
                            'finance'        => 'Finance',
                            'crm'            => 'CRM',
                            'marketing'      => 'Marketing',
                            'operations'     => 'Operations',
                            'lms'            => 'LMS',
                            'ecommerce'      => 'E-Commerce',
                            'communications' => 'Communications',
                            'it'             => 'IT',
                            'legal'          => 'Legal',
                            'analytics'      => 'Analytics',
                        ])
                        ->required()
                        ->default('general'),

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
                TextColumn::make('question')
                    ->limit(60)
                    ->searchable(),

                TextColumn::make('context')
                    ->badge()
                    ->color('primary'),

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
            'index'  => ListFaqEntries::route('/'),
            'create' => CreateFaqEntry::route('/create'),
            'edit'   => EditFaqEntry::route('/{record}/edit'),
        ];
    }
}
