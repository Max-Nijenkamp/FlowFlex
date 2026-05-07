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

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::Leave;

    protected static ?int $navigationSort = 1;

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
            Section::make('Leave Type Details')
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
                        ->label('Paid Leave')
                        ->default(true),

                    Toggle::make('requires_approval')
                        ->label('Requires Approval')
                        ->default(true),

                    Toggle::make('allow_half_day')
                        ->label('Allow Half Day')
                        ->default(true),

                    TextInput::make('min_notice_days')
                        ->label('Minimum Notice (days)')
                        ->numeric()
                        ->default(0),

                    Toggle::make('is_active')
                        ->label('Active')
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
                    ->label('Paid')
                    ->boolean(),

                IconColumn::make('requires_approval')
                    ->label('Approval Required')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
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
