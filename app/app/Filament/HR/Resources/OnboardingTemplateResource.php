<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\HR\OnboardingTemplate;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class OnboardingTemplateResource extends Resource
{
    protected static ?string $model = OnboardingTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Employees';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.onboarding.manage-templates')
            && app(BillingServiceInterface::class)->hasModule('hr.onboarding');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(150),
            Toggle::make('is_default')->label('Company default'),
            Repeater::make('tasks')
                ->relationship('tasks')
                ->schema([
                    TextInput::make('title')->required(),
                    Select::make('assigned_role')
                        ->options(['hr' => 'HR', 'it' => 'IT', 'manager' => 'Manager', 'employee' => 'Employee'])
                        ->required(),
                    TextInput::make('order')->numeric()->default(0),
                ])
                ->orderColumn('order')
                ->minItems(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                IconColumn::make('is_default')->boolean(),
                TextColumn::make('tasks_count')->counts('tasks')->label('Tasks'),
            ])
            ->recordActions([EditAction::make()]);
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
