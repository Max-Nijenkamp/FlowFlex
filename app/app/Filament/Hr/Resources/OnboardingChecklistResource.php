<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Resources\OnboardingChecklistResource\Pages\CreateOnboardingChecklist;
use App\Filament\Hr\Resources\OnboardingChecklistResource\Pages\EditOnboardingChecklist;
use App\Filament\Hr\Resources\OnboardingChecklistResource\Pages\ListOnboardingChecklists;
use App\Models\HR\Employee;
use App\Models\HR\OnboardingChecklist;
use App\Models\HR\OnboardingTemplate;
use App\Services\Core\BillingService;
use App\Support\Services\CompanyContext;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OnboardingChecklistResource extends Resource
{
    protected static ?string $model = OnboardingChecklist::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-check-badge';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Employees';
    }

    public static function getNavigationLabel(): string
    {
        return 'Onboarding Checklists';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function canAccess(): bool
    {
        if (! auth()->check()) {
            return false;
        }
        $ctx = app(CompanyContext::class);
        if (! $ctx->hasCompany()) {
            return false;
        }

        return app(BillingService::class)
            ->enforceModuleAccess($ctx->current(), 'hr.onboarding');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Checklist Details')->schema([
                Select::make('employee_id')
                    ->label('Employee')
                    ->options(fn () => Employee::withoutGlobalScopes()
                        ->where('company_id', app(CompanyContext::class)->currentId())
                        ->get()
                        ->mapWithKeys(fn ($e) => [$e->id => $e->full_name])
                        ->toArray())
                    ->searchable()
                    ->required(),
                Select::make('template_id')
                    ->label('Template')
                    ->options(fn () => OnboardingTemplate::withoutGlobalScopes()
                        ->where('company_id', app(CompanyContext::class)->currentId())
                        ->where('is_active', true)
                        ->pluck('name', 'id')
                        ->toArray())
                    ->nullable(),
                DatePicker::make('start_date')->required(),
                DatePicker::make('target_completion_date'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.first_name')
                    ->label('Employee')
                    ->formatStateUsing(fn (OnboardingChecklist $record) => $record->employee?->full_name ?? '—')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('template.name')->label('Template')->placeholder('Custom'),
                TextColumn::make('start_date')->date()->sortable(),
                TextColumn::make('target_completion_date')->date()->placeholder('—'),
                TextColumn::make('progress_percentage')
                    ->label('Progress')
                    ->suffix('%')
                    ->formatStateUsing(fn (OnboardingChecklist $record) => $record->progress_percentage),
                TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime()
                    ->placeholder('In progress'),
            ])
            ->actions([
                Action::make('complete_item')
                    ->label('Manage Items')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->url(fn (OnboardingChecklist $record) => static::getUrl('edit', ['record' => $record])),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOnboardingChecklists::route('/'),
            'create' => CreateOnboardingChecklist::route('/create'),
            'edit'   => EditOnboardingChecklist::route('/{record}/edit'),
        ];
    }
}
