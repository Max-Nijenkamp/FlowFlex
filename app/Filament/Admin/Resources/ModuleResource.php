<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Enums\NavigationGroup;
use App\Filament\Admin\Resources\ModuleResource\Pages\EditModule;
use App\Filament\Admin\Resources\ModuleResource\Pages\ListModules;
use App\Filament\Admin\Resources\ModuleResource\Pages\ViewModule;
use App\Filament\Admin\Resources\ModuleResource\RelationManagers\SubModulesRelationManager;
use App\Models\Module;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Support\Colors\Color;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ModuleResource extends Resource
{
    protected static ?string $model = Module::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::Platform;

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Platform->label();
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.modules.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.modules.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('admin.resources.modules.sections.module'))
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('key')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->helperText('kebab-case identifier, e.g. hr-payroll'),

                    Textarea::make('description')
                        ->maxLength(500)
                        ->columnSpanFull(),

                    TextInput::make('icon')
                        ->placeholder('heroicon-o-puzzle-piece')
                        ->maxLength(100),

                    TextInput::make('color')
                        ->placeholder('#2199C8')
                        ->maxLength(20),
                ]),

            Section::make(__('admin.resources.modules.sections.availability'))
                ->schema([
                    Toggle::make('is_available')
                        ->label(__('admin.resources.modules.fields.available'))
                        ->helperText('Disabling hides this module from all tenant module catalogues.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->description(fn (Module $record) => $record->key),

                TextColumn::make('domain')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (?string $state) => $state ? ucfirst($state) : '—')
                    ->color(fn (?string $state) => match ($state) {
                        'core'           => Color::hex('#2199C8'),
                        'hr'             => Color::hex('#7C3AED'),
                        'projects'       => Color::hex('#4F46E5'),
                        'finance'        => Color::hex('#059669'),
                        'crm'            => Color::hex('#2563EB'),
                        'marketing'      => Color::hex('#DB2777'),
                        'operations'     => Color::hex('#D97706'),
                        'analytics'      => Color::hex('#9333EA'),
                        'it'             => Color::hex('#475569'),
                        'legal'          => Color::hex('#DC2626'),
                        'ecommerce'      => Color::hex('#0D9488'),
                        'communications' => Color::hex('#0284C7'),
                        'learning'       => Color::hex('#EA580C'),
                        default          => Color::hex('#6B7280'),
                    }),

                TextColumn::make('panel_id')
                    ->label('Panel')
                    ->badge()
                    ->color('gray')
                    ->fontFamily(FontFamily::Mono)
                    ->formatStateUsing(fn (?string $state) => $state ?? 'core'),

                TextColumn::make('sub_modules_count')
                    ->label(__('admin.resources.modules.columns.sub_modules'))
                    ->counts('subModules')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                IconColumn::make('is_core')
                    ->label(__('admin.resources.modules.columns.core'))
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                IconColumn::make('is_available')
                    ->label(__('admin.resources.modules.columns.available'))
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->striped()
            ->filters([
                SelectFilter::make('domain')
                    ->options([
                        'core'           => 'Core Platform',
                        'hr'             => 'HR & People',
                        'projects'       => 'Projects & Work',
                        'finance'        => 'Finance',
                        'crm'            => 'CRM & Sales',
                        'marketing'      => 'Marketing',
                        'operations'     => 'Operations',
                        'analytics'      => 'Analytics',
                        'it'             => 'IT & Security',
                        'legal'          => 'Legal',
                        'ecommerce'      => 'E-commerce',
                        'communications' => 'Communications',
                        'learning'       => 'Learning',
                    ]),

                TernaryFilter::make('is_core')
                    ->label('Core only'),

                TernaryFilter::make('is_available')
                    ->label('Available'),
            ])
            ->actions([
                Action::make('toggleAvailability')
                    ->label(fn (Module $record) => $record->is_available ? 'Disable' : 'Enable')
                    ->icon(fn (Module $record) => $record->is_available ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Module $record) => $record->is_available ? 'danger' : 'success')
                    ->disabled(fn (Module $record) => $record->is_core)
                    ->requiresConfirmation()
                    ->modalHeading(fn (Module $record) => $record->is_available ? "Disable {$record->name}?" : "Enable {$record->name}?")
                    ->modalDescription(fn (Module $record) => $record->is_available
                        ? 'This will hide the module from all tenants that have not enabled it.'
                        : 'This will make the module available for tenants to activate.'
                    )
                    ->action(function (Module $record): void {
                        if ($record->is_core) {
                            Notification::make()
                                ->danger()
                                ->title('Core modules cannot be disabled')
                                ->send();
                            return;
                        }

                        $record->update(['is_available' => ! $record->is_available]);

                        Notification::make()
                            ->success()
                            ->title($record->is_available ? "{$record->name} enabled" : "{$record->name} disabled")
                            ->send();
                    }),

                ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelationManagers(): array
    {
        return [
            SubModulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListModules::route('/'),
            'view'  => ViewModule::route('/{record}'),
            'edit'  => EditModule::route('/{record}/edit'),
        ];
    }
}
