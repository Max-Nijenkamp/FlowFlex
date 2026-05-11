<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Resources\OnboardingTemplateResource\Pages\CreateOnboardingTemplate;
use App\Filament\Hr\Resources\OnboardingTemplateResource\Pages\EditOnboardingTemplate;
use App\Filament\Hr\Resources\OnboardingTemplateResource\Pages\ListOnboardingTemplates;
use App\Models\HR\OnboardingTemplate;
use App\Services\Core\BillingService;
use App\Support\Services\CompanyContext;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OnboardingTemplateResource extends Resource
{
    protected static ?string $model = OnboardingTemplate::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-clipboard-document-list';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Employees';
    }

    public static function getNavigationLabel(): string
    {
        return 'Onboarding Templates';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
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
            Section::make('Template Details')->schema([
                TextInput::make('name')->required()->maxLength(100)->columnSpanFull(),
                Textarea::make('description')->rows(3)->columnSpanFull(),
                Toggle::make('is_active')->default(true),
            ])->columns(2),

            Section::make('Tasks')->schema([
                Repeater::make('tasks')
                    ->relationship()
                    ->schema([
                        TextInput::make('title')->required()->maxLength(200),
                        Textarea::make('description')->rows(2),
                        Select::make('assignee_role')
                            ->options([
                                'hr_manager' => 'HR Manager',
                                'it'         => 'IT',
                                'manager'    => 'Manager',
                                'employee'   => 'Employee',
                            ])
                            ->nullable(),
                        TextInput::make('due_days_after_hire')
                            ->numeric()
                            ->default(1)
                            ->minValue(0),
                        Toggle::make('is_required')->default(true),
                        TextInput::make('sort_order')->numeric()->default(0),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->orderColumn('sort_order'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('description')->limit(60)->placeholder('—'),
                TextColumn::make('tasks_count')
                    ->label('Tasks')
                    ->counts('tasks'),
                IconColumn::make('is_active')->boolean()->label('Active'),
                TextColumn::make('created_at')->date()->sortable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOnboardingTemplates::route('/'),
            'create' => CreateOnboardingTemplate::route('/create'),
            'edit'   => EditOnboardingTemplate::route('/{record}/edit'),
        ];
    }
}
