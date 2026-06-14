<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\HR\CompensationBand;
use App\Models\HR\Department;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
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
        return $schema->components([
            Section::make('Band')
                ->columns(2)
                ->components([
                    TextInput::make('job_grade')->required()->maxLength(100),
                    Select::make('department_id')->label('Department')
                        ->options(fn () => Department::query()->pluck('name', 'id'))
                        ->nullable(),
                ]),
            Section::make('Salary range')
                ->columns(2)
                ->components([
                    TextInput::make('min_salary_cents')->label('Min salary (cents)')
                        ->numeric()->integer()->minValue(0)->required(),
                    TextInput::make('mid_salary_cents')->label('Mid salary (cents)')
                        ->numeric()->integer()->minValue(0)->required()
                        ->gte('min_salary_cents'),
                    TextInput::make('max_salary_cents')->label('Max salary (cents)')
                        ->numeric()->integer()->minValue(0)->required()
                        ->gte('mid_salary_cents'),
                    TextInput::make('currency')->length(3)->default('EUR')->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('job_grade'),
                TextColumn::make('min_salary_cents')->label('Min')->formatStateUsing(fn (int $state) => '€'.number_format($state / 100)),
                TextColumn::make('mid_salary_cents')->label('Mid')->formatStateUsing(fn (int $state) => '€'.number_format($state / 100)),
                TextColumn::make('max_salary_cents')->label('Max')->formatStateUsing(fn (int $state) => '€'.number_format($state / 100)),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => CompensationBandResource\Pages\ListCompensationBands::route('/'),
        ];
    }
}
