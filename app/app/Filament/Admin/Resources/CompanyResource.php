<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CompanyResource\Pages;
use App\Filament\Admin\Resources\CompanyResource\RelationManagers\InvoicesRelationManager;
use App\Filament\Admin\Resources\CompanyResource\RelationManagers\ModulesRelationManager;
use App\Models\Company;
use App\Services\BillingService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * FlowFlex staff console (core.staff-console): manage, provision and
 * suspend customer companies. Admin guard only — cross-tenant by design.
 */
class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Companies';

    protected static ?string $modelLabel = 'company';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('slug')->disabled()->helperText('Set at provisioning; the company edits it in their settings.'),
            Select::make('subscription_status')
                ->options(['trial' => 'Trial', 'active' => 'Active', 'suspended' => 'Suspended', 'cancelled' => 'Cancelled'])
                ->disabled()
                ->helperText('Changes flow through billing (suspend action / dunning), never direct edits.'),
            Select::make('timezone')
                ->options(array_combine(timezone_identifiers_list(), timezone_identifiers_list()))
                ->searchable(),
            Select::make('locale')->options(['en' => 'English', 'nl' => 'Nederlands']),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug')->searchable(),
                TextColumn::make('subscription_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success', 'trial' => 'info', 'suspended' => 'danger', default => 'gray',
                    }),
                TextColumn::make('users_count')->label('Users')->counts('users'),
                TextColumn::make('created_at')->label('Since')->date('d M Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('subscription_status')
                    ->options(['trial' => 'Trial', 'active' => 'Active', 'suspended' => 'Suspended']),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('suspend')
                    ->icon('heroicon-o-pause-circle')
                    ->color('danger')
                    ->visible(fn (Company $record): bool => $record->subscription_status !== 'suspended')
                    ->schema([
                        Textarea::make('reason')->required()->maxLength(500),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Suspend workspace')
                    ->action(function (Company $record, array $data): void {
                        app(BillingService::class)->suspend($record->id, (string) $data['reason']);
                        Notification::make()->success()->title('Workspace suspended')->send();
                    }),
                Action::make('reactivate')
                    ->icon('heroicon-o-play-circle')
                    ->color('success')
                    ->visible(fn (Company $record): bool => $record->subscription_status === 'suspended')
                    ->requiresConfirmation()
                    ->action(function (Company $record): void {
                        $record->update(['subscription_status' => 'active']);
                        Notification::make()->success()->title('Workspace reactivated')->send();
                    }),
            ])
            ->emptyStateHeading('No companies yet')
            ->emptyStateDescription('Provision the first customer with the Create button.');
    }

    public static function getRelations(): array
    {
        return [
            ModulesRelationManager::class,
            InvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
