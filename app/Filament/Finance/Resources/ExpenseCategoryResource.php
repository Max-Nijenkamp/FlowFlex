<?php

namespace App\Filament\Finance\Resources;

use App\Filament\Finance\Enums\NavigationGroup;
use App\Filament\Finance\Resources\ExpenseCategoryResource\Pages\CreateExpenseCategory;
use App\Filament\Finance\Resources\ExpenseCategoryResource\Pages\EditExpenseCategory;
use App\Filament\Finance\Resources\ExpenseCategoryResource\Pages\ListExpenseCategories;
use App\Models\Finance\ExpenseCategory;
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

class ExpenseCategoryResource extends Resource
{
    protected static ?string $model = ExpenseCategory::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Expenses->label();
    }

    public static function getModelLabel(): string
    {
        return __('finance.resources.expense_categories.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('finance.resources.expense_categories.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('finance.expense-categories.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('finance.expense-categories.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('finance.expense-categories.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('finance.expense-categories.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('finance.resources.expense_categories.sections.details'))
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    Textarea::make('description')
                        ->nullable()
                        ->rows(2),

                    TextInput::make('monthly_limit')
                        ->numeric()
                        ->nullable()
                        ->prefix('€'),

                    Toggle::make('is_active')
                        ->default(true),
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

                TextColumn::make('description')
                    ->limit(50)
                    ->placeholder('—'),

                TextColumn::make('monthly_limit')
                    ->numeric(decimalPlaces: 2)
                    ->prefix('€ ')
                    ->placeholder('No limit'),

                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->defaultSort('name')
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
            'index'  => ListExpenseCategories::route('/'),
            'create' => CreateExpenseCategory::route('/create'),
            'edit'   => EditExpenseCategory::route('/{record}/edit'),
        ];
    }
}
