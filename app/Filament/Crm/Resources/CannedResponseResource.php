<?php

namespace App\Filament\Crm\Resources;

use App\Filament\Crm\Enums\NavigationGroup;
use App\Filament\Crm\Resources\CannedResponseResource\Pages\CreateCannedResponse;
use App\Filament\Crm\Resources\CannedResponseResource\Pages\EditCannedResponse;
use App\Filament\Crm\Resources\CannedResponseResource\Pages\ListCannedResponses;
use App\Models\Crm\CannedResponse;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CannedResponseResource extends Resource
{
    protected static ?string $model = CannedResponse::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Support->label();
    }

    public static function getModelLabel(): string
    {
        return __('crm.resources.canned_responses.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm.resources.canned_responses.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('crm.canned-responses.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('crm.canned-responses.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('crm.canned-responses.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('crm.canned-responses.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('crm.resources.canned_responses.sections.details'))
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),

                    Textarea::make('body')
                        ->required()
                        ->rows(5),

                    Toggle::make('is_shared')
                        ->label(__('crm.resources.canned_responses.fields.is_shared'))
                        ->default(true),
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

                TextColumn::make('body')
                    ->limit(60),

                IconColumn::make('is_shared')
                    ->label(__('crm.resources.canned_responses.columns.shared'))
                    ->boolean(),
            ])
            ->defaultSort('title')
            ->striped()
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
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
            'index'  => ListCannedResponses::route('/'),
            'create' => CreateCannedResponse::route('/create'),
            'edit'   => EditCannedResponse::route('/{record}/edit'),
        ];
    }
}
