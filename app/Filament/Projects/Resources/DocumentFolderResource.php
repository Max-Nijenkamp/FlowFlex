<?php

namespace App\Filament\Projects\Resources;

use App\Filament\Projects\Enums\NavigationGroup;
use App\Filament\Projects\Resources\DocumentFolderResource\Pages\CreateDocumentFolder;
use App\Filament\Projects\Resources\DocumentFolderResource\Pages\EditDocumentFolder;
use App\Filament\Projects\Resources\DocumentFolderResource\Pages\ListDocumentFolders;
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

class DocumentFolderResource extends Resource
{
    protected static ?string $model = DocumentFolder::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-folder';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::Documents;

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('projects.document-folders.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('projects.document-folders.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('projects.document-folders.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('projects.document-folders.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Folder Details')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    Select::make('parent_id')
                        ->label('Parent Folder')
                        ->options(fn () => DocumentFolder::query()->pluck('name', 'id')->toArray())
                        ->nullable()
                        ->searchable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->sortable(),

                TextColumn::make('parent.name')
                    ->label('Parent')
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

    public static function getPages(): array
    {
        return [
            'index'  => ListDocumentFolders::route('/'),
            'create' => CreateDocumentFolder::route('/create'),
            'edit'   => EditDocumentFolder::route('/{record}/edit'),
        ];
    }
}
