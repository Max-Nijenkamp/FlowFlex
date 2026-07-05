<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources;

use App\Models\Hr\OnboardingPlan;
use App\Models\User;
use App\Services\BillingService;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Onboarding progress dashboard (hr.onboarding/progress-dashboard):
 * every running plan with its completion percentage; drill into the
 * checklist to work the tasks.
 */
class OnboardingPlanResource extends Resource
{
    protected static ?string $model = OnboardingPlan::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rocket-launch';

    protected static string|\UnitEnum|null $navigationGroup = 'Onboarding';

    protected static ?string $navigationLabel = 'Plans';

    protected static ?string $modelLabel = 'onboarding plan';

    protected static ?string $slug = 'onboarding';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('hr.onboarding.view-any')
            && app(BillingService::class)->hasModule('hr.onboarding');
    }

    public static function canCreate(): bool
    {
        return false; // plans generate from EmployeeHired, never by hand
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['employee', 'template']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('New hire')
                    ->state(fn (OnboardingPlan $record): string => $record->employee()->first()->full_name ?? '—')
                    ->searchable(),
                TextColumn::make('template.name')->label('Template'),
                TextColumn::make('started_at')->label('Started')->date('d M Y')->sortable(),
                TextColumn::make('progress')
                    ->label('Progress')
                    ->badge()
                    ->state(fn (OnboardingPlan $record): string => $record->progressPercent().'%')
                    ->color(fn (OnboardingPlan $record): string => match (true) {
                        $record->completed_at !== null => 'success',
                        $record->progressPercent() >= 50 => 'info',
                        default => 'warning',
                    }),
                TextColumn::make('completed_at')->label('Completed')->date('d M Y')->placeholder('—'),
            ])
            ->defaultSort('started_at', 'desc')
            ->filters([
                TernaryFilter::make('completed')
                    ->label('Completed')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('completed_at'),
                        false: fn (Builder $query) => $query->whereNull('completed_at'),
                    ),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('Checklist')
                    ->icon('heroicon-o-list-bullet')
                    ->url(fn (OnboardingPlan $record): string => static::getUrl('tasks', ['record' => $record])),
            ])
            ->emptyStateHeading('No onboardings yet')
            ->emptyStateDescription('Hire an employee and a plan appears here from the matching template.');
    }

    public static function getPages(): array
    {
        return [
            'index' => OnboardingPlanResource\Pages\ListOnboardingPlans::route('/'),
            'tasks' => OnboardingPlanResource\Pages\ManagePlanTasks::route('/{record}/tasks'),
        ];
    }
}
