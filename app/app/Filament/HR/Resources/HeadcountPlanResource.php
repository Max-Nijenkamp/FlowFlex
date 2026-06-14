<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\HR\Department;
use App\Models\HR\HeadcountPlan;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class HeadcountPlanResource extends Resource
{
    protected static ?string $model = HeadcountPlan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Analytics';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.workforce.view-any')
            && app(BillingServiceInterface::class)->hasModule('hr.workforce');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Plan')
                ->columns(2)
                ->components([
                    TextInput::make('period')->required()->maxLength(20)
                        ->helperText('e.g. 2026-Q3 or 2027'),
                    Select::make('department_id')->label('Department')
                        ->options(fn () => Department::query()->pluck('name', 'id'))
                        ->nullable(),
                ]),
            Section::make('Targets & budget')
                ->columns(2)
                ->components([
                    TextInput::make('target_headcount')->numeric()->integer()->minValue(0)->required(),
                    TextInput::make('expected_attrition')->numeric()->integer()->minValue(0)->default(0)->required(),
                    TextInput::make('budgeted_cost_cents')->label('Budgeted cost (cents)')
                        ->numeric()->integer()->minValue(0)->default(0)->required(),
                    TextInput::make('currency')->length(3)->default('EUR')->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('period'),
                TextColumn::make('target_headcount')->label('Target'),
                TextColumn::make('budgeted_cost_cents')->label('Budget')->formatStateUsing(fn (int $state) => '€'.number_format($state / 100)),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => HeadcountPlanResource\Pages\ListHeadcountPlans::route('/'),
        ];
    }
}
