<?php

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Enums\NavigationGroup;
use App\Filament\Hr\Resources\LeaveTypeResource\Pages\CreateLeaveType;
use App\Filament\Hr\Resources\LeaveTypeResource\Pages\EditLeaveType;
use App\Filament\Hr\Resources\LeaveTypeResource\Pages\ListLeaveTypes;
use App\Models\Hr\LeaveType;
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
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeaveTypeResource extends Resource
{
    protected static ?string $model = LeaveType::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Leave->label();
    }

    public static function getModelLabel(): string
    {
        return __('hr.resources.leave_types.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('hr.resources.leave_types.plural');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr.leave-types.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr.leave-types.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('hr.leave-types.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('hr.leave-types.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('hr.resources.leave_types.sections.details'))
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('code')
                        ->required()
                        ->maxLength(10)
                        ->hint('Use uppercase, e.g. AL, SL'),

                    Textarea::make('description')
                        ->nullable()
                        ->rows(3),

                    Toggle::make('is_paid')
                        ->label(__('hr.resources.leave_types.fields.paid_leave'))
                        ->default(true),

                    Toggle::make('requires_approval')
                        ->label(__('hr.resources.leave_types.fields.requires_approval'))
                        ->default(true),

                    Toggle::make('allow_half_day')
                        ->label(__('hr.resources.leave_types.fields.allow_half_day'))
                        ->default(true),

                    TextInput::make('min_notice_days')
                        ->label(__('hr.resources.leave_types.fields.min_notice_days'))
                        ->numeric()
                        ->default(0),

                    Toggle::make('is_active')
                        ->label(__('hr.resources.leave_types.fields.is_active'))
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
                    ->weight(FontWeight::Bold)
                    ->sortable(),

                TextColumn::make('code')
                    ->badge(),

                IconColumn::make('is_paid')
                    ->label(__('hr.resources.leave_types.columns.paid'))
                    ->boolean(),

                IconColumn::make('requires_approval')
                    ->label(__('hr.resources.leave_types.columns.approval_required'))
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label(__('hr.resources.leave_types.columns.is_active'))
                    ->boolean(),
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
            'index'  => ListLeaveTypes::route('/'),
            'create' => CreateLeaveType::route('/create'),
            'edit'   => EditLeaveType::route('/{record}/edit'),
        ];
    }
}
