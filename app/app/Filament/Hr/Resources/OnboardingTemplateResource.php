<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources;

use App\Models\Hr\Department;
use App\Models\Hr\OnboardingTemplate;
use App\Models\User;
use App\Services\BillingService;
use App\Support\Services\CompanyContext;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/**
 * Onboarding templates (hr.onboarding/onboarding-templates): tasks with
 * an assigned role (hr / it = equipment / manager / employee) and
 * relative due days (30/60/90 = milestone check-ins).
 */
class OnboardingTemplateResource extends Resource
{
    protected static ?string $model = OnboardingTemplate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Onboarding';

    protected static ?string $navigationLabel = 'Templates';

    protected static ?string $slug = 'onboarding-templates';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('hr.onboarding.manage')
            && app(BillingService::class)->hasModule('hr.onboarding');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Template')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->maxLength(160),
                    Select::make('department_id')
                        ->label('Department')
                        ->options(fn (): array => Department::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->placeholder('Company-wide')
                        ->helperText('Department templates win over the company default at hire.'),
                    Toggle::make('is_default')
                        ->label('Company default')
                        ->helperText('Used when no department template matches.'),
                    Textarea::make('description')->rows(2)->columnSpanFull(),
                    Repeater::make('tasks')
                        ->relationship('tasks')
                        ->columnSpanFull()
                        ->columns(4)
                        ->orderColumn('order')
                        ->reorderable()
                        ->defaultItems(3)
                        ->schema([
                            TextInput::make('title')->required()->columnSpan(2),
                            Select::make('assigned_role')
                                ->label('Assignee')
                                ->options([
                                    'hr' => 'HR', 'it' => 'IT (equipment)',
                                    'manager' => 'Manager', 'employee' => 'Employee',
                                ])
                                ->default('hr')
                                ->required(),
                            TextInput::make('due_days_after_start')
                                ->label('Due (days after start)')
                                ->numeric()->minValue(0)->maxValue(365),
                        ])
                        ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                            $data['company_id'] = app(CompanyContext::class)->currentId();

                            return $data;
                        }),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('department.name')->label('Department')->placeholder('Company-wide'),
                IconColumn::make('is_default')->label('Default')->boolean(),
                TextColumn::make('tasks_count')->label('Tasks')->counts('tasks'),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('No templates yet')
            ->emptyStateDescription('Build a checklist once — every new hire gets a plan from it automatically.');
    }

    public static function getPages(): array
    {
        return [
            'index' => OnboardingTemplateResource\Pages\ListOnboardingTemplates::route('/'),
            'create' => OnboardingTemplateResource\Pages\CreateOnboardingTemplate::route('/create'),
            'edit' => OnboardingTemplateResource\Pages\EditOnboardingTemplate::route('/{record}/edit'),
        ];
    }
}
