<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Contracts\HR\OnboardingServiceInterface;
use App\Models\HR\Employee;
use App\Models\HR\OnboardingPlan;
use App\Models\HR\OnboardingTemplate;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class OnboardingPlanResource extends Resource
{
    protected static ?string $model = OnboardingPlan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRocketLaunch;

    protected static string|UnitEnum|null $navigationGroup = 'Employees';

    protected static ?string $modelLabel = 'onboarding';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.onboarding.view-any')
            && app(BillingServiceInterface::class)->hasModule('hr.onboarding');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Onboarding')
                ->columns(2)
                ->components([
                    Select::make('employee_id')->label('Employee')
                        ->options(fn () => Employee::query()->get()->pluck('full_name', 'id'))
                        ->searchable()
                        ->required()
                        ->disabled(fn (string $operation): bool => $operation === 'edit'),
                    Select::make('template_id')->label('Template')
                        ->options(fn () => OnboardingTemplate::query()->pluck('name', 'id'))
                        ->required()
                        ->disabled(fn (string $operation): bool => $operation === 'edit'),
                    DateTimePicker::make('started_at')->default(now())->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest('started_at'))
            ->columns([
                TextColumn::make('employee.full_name')->label('Employee')
                    ->state(fn (OnboardingPlan $r) => $r->employee->full_name),
                TextColumn::make('started_at')->dateTime(),
                TextColumn::make('progress')->label('Progress')
                    ->state(fn (OnboardingPlan $r) => (app(OnboardingServiceInterface::class)->progress($r->id) * 100).'%'),
                TextColumn::make('completed_at')->dateTime()->placeholder('In progress'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => OnboardingPlanResource\Pages\ListOnboardingPlans::route('/'),
        ];
    }
}
