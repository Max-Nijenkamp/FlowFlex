<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\HR\CompensationBand;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class CompensationBandResource extends Resource
{
    protected static ?string $model = CompensationBand::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static string|UnitEnum|null $navigationGroup = 'Payroll';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.compensation.view-any')
            && app(BillingServiceInterface::class)->hasModule('hr.compensation');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('job_grade'),
                TextColumn::make('min_salary_cents')->label('Min')->formatStateUsing(fn (int $state) => '€'.number_format($state / 100)),
                TextColumn::make('mid_salary_cents')->label('Mid')->formatStateUsing(fn (int $state) => '€'.number_format($state / 100)),
                TextColumn::make('max_salary_cents')->label('Max')->formatStateUsing(fn (int $state) => '€'.number_format($state / 100)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => CompensationBandResource\Pages\ListCompensationBands::route('/'),
        ];
    }
}
