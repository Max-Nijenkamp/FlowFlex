<?php

namespace App\Filament\Hr\Resources;

use App\Enums\Hr\OnboardingFlowStatus;
use App\Filament\Hr\Enums\NavigationGroup;
use App\Filament\Hr\Resources\OnboardingFlowResource\Pages\CreateOnboardingFlow;
use App\Filament\Hr\Resources\OnboardingFlowResource\Pages\EditOnboardingFlow;
use App\Filament\Hr\Resources\OnboardingFlowResource\Pages\ListOnboardingFlows;
use App\Models\Hr\Employee;
use App\Models\Hr\OnboardingFlow;
use App\Models\Hr\OnboardingTemplate;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OnboardingFlowResource extends Resource
{
    protected static ?string $model = OnboardingFlow::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-rocket-launch';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::Onboarding;

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr.onboarding-flows.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr.onboarding-flows.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('hr.onboarding-flows.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('hr.onboarding-flows.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Flow Details')
                ->schema([
                    Select::make('employee_id')
                        ->label('Employee')
                        ->relationship('employee', 'first_name')
                        ->getOptionLabelFromRecordUsing(fn (Employee $record) => trim("{$record->first_name} {$record->last_name}"))
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('template_id')
                        ->label('Template')
                        ->options(fn () => OnboardingTemplate::query()->where('is_active', true)->pluck('name', 'id')->toArray())
                        ->nullable()
                        ->searchable(),

                    Select::make('status')
                        ->options(
                            collect(OnboardingFlowStatus::cases())
                                ->mapWithKeys(fn (OnboardingFlowStatus $case) => [$case->value => $case->label()])
                                ->toArray()
                        )
                        ->default(OnboardingFlowStatus::NotStarted->value)
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_name')
                    ->label('Employee')
                    ->getStateUsing(fn (OnboardingFlow $record) => trim("{$record->employee?->first_name} {$record->employee?->last_name}")),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?OnboardingFlowStatus $state) => $state?->label())
                    ->color(fn (?OnboardingFlowStatus $state) => $state?->color()),

                TextColumn::make('started_at')
                    ->label('Started')
                    ->dateTime('d M Y H:i')
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
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
        return parent::getEloquentQuery()->with(['employee']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOnboardingFlows::route('/'),
            'create' => CreateOnboardingFlow::route('/create'),
            'edit'   => EditOnboardingFlow::route('/{record}/edit'),
        ];
    }
}
