<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\HR\JobRequisition;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class JobRequisitionResource extends Resource
{
    protected static ?string $model = JobRequisition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static string|UnitEnum|null $navigationGroup = 'Recruitment';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.recruitment.view-any')
            && app(BillingServiceInterface::class)->hasModule('hr.recruitment');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('headcount'),
                TextColumn::make('slug')->label('Careers URL')->formatStateUsing(fn (string $state) => url('/careers/'.$state)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => JobRequisitionResource\Pages\ListJobRequisitions::route('/'),
        ];
    }
}
