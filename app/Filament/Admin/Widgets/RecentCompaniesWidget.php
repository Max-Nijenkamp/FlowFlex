<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Company;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentCompaniesWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Recently Registered Companies';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Company::withoutGlobalScopes()
                    ->withCount(['modules' => fn (Builder $q) => $q->where('company_module.is_enabled', true)])
                    ->withCount('tenants')
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->weight(FontWeight::Medium)
                    ->description(fn (Company $record) => $record->slug),

                TextColumn::make('email')
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('tenants_count')
                    ->label('Users')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('modules_count')
                    ->label('Modules')
                    ->badge()
                    ->color('success'),

                IconColumn::make('is_enabled')
                    ->label('Active')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label('Registered')
                    ->since()
                    ->sortable()
                    ->color('gray'),
            ])
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->label('Manage')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (Company $record) => route('filament.admin.resources.companies.edit', $record))
                    ->openUrlInNewTab(),
            ])
            ->paginated(false)
            ->striped();
    }
}
