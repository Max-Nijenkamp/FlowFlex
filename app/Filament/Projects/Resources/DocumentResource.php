<?php

namespace App\Filament\Projects\Resources;

use App\Filament\Projects\Enums\NavigationGroup;
use App\Filament\Projects\Resources\DocumentResource\Pages\CreateDocument;
use App\Filament\Projects\Resources\DocumentResource\Pages\EditDocument;
use App\Filament\Projects\Resources\DocumentResource\Pages\ListDocuments;
use App\Models\Projects\Document;
use App\Models\Projects\DocumentFolder;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Documents->label();
    }

    public static function getModelLabel(): string
    {
        return __('projects.resources.documents.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('projects.resources.documents.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('projects.documents.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('projects.documents.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('projects.documents.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('projects.documents.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('projects.resources.documents.sections.details'))
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),

                    Select::make('folder_id')
                        ->label(__('projects.resources.documents.fields.folder_id'))
                        ->relationship('folder', 'name')
                        ->nullable()
                        ->searchable()
                        ->preload(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->sortable(),

                TextColumn::make('folder.name')
                    ->label(__('projects.resources.documents.columns.folder'))
                    ->placeholder('Root'),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->striped()
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['folder']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'edit'   => EditDocument::route('/{record}/edit'),
        ];
    }
}
