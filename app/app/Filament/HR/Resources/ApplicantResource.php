<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\HR\Applicant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ApplicantResource extends Resource
{
    protected static ?string $model = Applicant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

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
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('full_name')->label('Name')->state(fn (Applicant $r) => $r->full_name),
                TextColumn::make('email'),
                TextColumn::make('requisition.title')->label('Role'),
                TextColumn::make('status')->badge(),
                TextColumn::make('source')->placeholder('—'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ApplicantResource\Pages\ListApplicants::route('/'),
        ];
    }
}
