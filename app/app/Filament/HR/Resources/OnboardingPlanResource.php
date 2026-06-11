<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Contracts\HR\OnboardingServiceInterface;
use App\Models\HR\OnboardingPlan;
use BackedEnum;
use Filament\Resources\Resource;
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

    public static function canCreate(): bool
    {
        return false; // plans start via the EmployeeHired listener / service
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->latest('started_at'))
            ->columns([
                TextColumn::make('employee.full_name')->label('Employee')
                    ->state(fn (OnboardingPlan $r) => $r->employee->full_name),
                TextColumn::make('started_at')->dateTime(),
                TextColumn::make('progress')->label('Progress')
                    ->state(fn (OnboardingPlan $r) => (app(OnboardingServiceInterface::class)->progress($r->id) * 100).'%'),
                TextColumn::make('completed_at')->dateTime()->placeholder('In progress'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => OnboardingPlanResource\Pages\ListOnboardingPlans::route('/'),
        ];
    }
}
