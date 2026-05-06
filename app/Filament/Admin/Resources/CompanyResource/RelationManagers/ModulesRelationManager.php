<?php

namespace App\Filament\Admin\Resources\CompanyResource\RelationManagers;

use App\Models\Module;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class ModulesRelationManager extends RelationManager
{
    protected static string $relationship = 'modules';

    protected static ?string $title = 'Modules';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        $company = $this->getOwnerRecord();

        return $table
            ->query(Module::where('is_available', true)->where('is_core', false)->orderBy('sort_order'))
            ->columns([
                TextColumn::make('name')
                    ->weight(FontWeight::Medium)
                    ->description(fn (Module $record) => $record->key),

                TextColumn::make('domain')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ? ucfirst($state) : '—')
                    ->color(fn (?string $state) => match ($state) {
                        'hr'             => Color::hex('#7C3AED'),
                        'projects'       => Color::hex('#4F46E5'),
                        'finance'        => Color::hex('#059669'),
                        'crm'            => Color::hex('#2563EB'),
                        'marketing'      => Color::hex('#DB2777'),
                        'operations'     => Color::hex('#D97706'),
                        'analytics'      => Color::hex('#9333EA'),
                        'it'             => Color::hex('#475569'),
                        'legal'          => Color::hex('#DC2626'),
                        'ecommerce'      => Color::hex('#0D9488'),
                        'communications' => Color::hex('#0284C7'),
                        'learning'       => Color::hex('#EA580C'),
                        default          => Color::hex('#6B7280'),
                    }),

                IconColumn::make('is_enabled_for_company')
                    ->label('Active')
                    ->getStateUsing(function (Module $record) use ($company) {
                        return $company->modules()
                            ->wherePivot('is_enabled', true)
                            ->where('modules.id', $record->id)
                            ->exists();
                    })
                    ->boolean(),
            ])
            ->striped()
            ->filters([
                TernaryFilter::make('is_available')->label('Available'),
            ])
            ->actions([
                Action::make('toggle')
                    ->label(function (Module $record) use ($company) {
                        $enabled = $company->modules()
                            ->wherePivot('is_enabled', true)
                            ->where('modules.id', $record->id)
                            ->exists();
                        return $enabled ? 'Disable' : 'Enable';
                    })
                    ->icon(function (Module $record) use ($company) {
                        $enabled = $company->modules()
                            ->wherePivot('is_enabled', true)
                            ->where('modules.id', $record->id)
                            ->exists();
                        return $enabled ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle';
                    })
                    ->color(function (Module $record) use ($company) {
                        $enabled = $company->modules()
                            ->wherePivot('is_enabled', true)
                            ->where('modules.id', $record->id)
                            ->exists();
                        return $enabled ? 'danger' : 'success';
                    })
                    ->requiresConfirmation()
                    ->action(function (Module $record) use ($company) {
                        $isEnabled = $company->modules()
                            ->wherePivot('is_enabled', true)
                            ->where('modules.id', $record->id)
                            ->exists();

                        $company->modules()->syncWithoutDetaching([
                            $record->id => [
                                'is_enabled'  => ! $isEnabled,
                                'enabled_at'  => ! $isEnabled ? now() : null,
                                'disabled_at' => ! $isEnabled ? null : now(),
                            ],
                        ]);

                        if ($record->panel_id) {
                            Cache::forget("company:{$company->id}:panel:{$record->panel_id}:access");
                        }

                        Notification::make()
                            ->success()
                            ->title($isEnabled ? "{$record->name} disabled" : "{$record->name} enabled")
                            ->send();
                    }),
            ])
            ->bulkActions([]);
    }
}
