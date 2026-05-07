<?php

namespace App\Filament\Crm\Resources;

use App\Filament\Crm\Enums\NavigationGroup;
use App\Filament\Crm\Resources\ChatbotRuleResource\Pages\CreateChatbotRule;
use App\Filament\Crm\Resources\ChatbotRuleResource\Pages\EditChatbotRule;
use App\Filament\Crm\Resources\ChatbotRuleResource\Pages\ListChatbotRules;
use App\Models\Crm\ChatbotRule;
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

class ChatbotRuleResource extends Resource
{
    protected static ?string $model = ChatbotRule::class;

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Support->label();
    }

    public static function getModelLabel(): string
    {
        return __('crm.resources.chatbot_rules.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm.resources.chatbot_rules.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('crm.chatbot-rules.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('crm.chatbot-rules.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('crm.chatbot-rules.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('crm.chatbot-rules.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('crm.resources.chatbot_rules.sections.details'))
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('trigger_keywords')
                        ->label(__('crm.resources.chatbot_rules.fields.trigger_keywords'))
                        ->helperText('Comma-separated keywords')
                        ->required()
                        ->afterStateHydrated(function (TextInput $component, $state): void {
                            if (is_array($state)) {
                                $component->state(implode(', ', $state));
                            }
                        })
                        ->dehydrateStateUsing(fn ($state) => array_map('trim', explode(',', $state ?? ''))),

                    Textarea::make('response_body')
                        ->label(__('crm.resources.chatbot_rules.fields.response_body'))
                        ->required()
                        ->rows(4),

                    TextInput::make('sort_order')
                        ->label(__('crm.resources.chatbot_rules.fields.sort_order'))
                        ->numeric()
                        ->default(0),

                    Toggle::make('is_active')
                        ->label(__('crm.resources.chatbot_rules.fields.is_active'))
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

                TextColumn::make('trigger_keywords')
                    ->label(__('crm.resources.chatbot_rules.columns.keywords'))
                    ->getStateUsing(fn (ChatbotRule $record) => is_array($record->trigger_keywords)
                        ? implode(', ', $record->trigger_keywords)
                        : $record->trigger_keywords
                    )
                    ->limit(50),

                IconColumn::make('is_active')
                    ->label(__('crm.resources.chatbot_rules.columns.is_active'))
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label(__('crm.resources.chatbot_rules.columns.order'))
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
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
            'index'  => ListChatbotRules::route('/'),
            'create' => CreateChatbotRule::route('/create'),
            'edit'   => EditChatbotRule::route('/{record}/edit'),
        ];
    }
}
