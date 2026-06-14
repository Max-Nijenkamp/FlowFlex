<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\HR\Applicant;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ApplicantResource extends Resource
{
    protected static ?string $model = Applicant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    protected static string|UnitEnum|null $navigationGroup = 'Recruitment';

    protected static ?string $recordTitleAttribute = 'last_name';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.recruitment.view-any')
            && app(BillingServiceInterface::class)->hasModule('hr.recruitment');
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var Applicant $record */
        return $record->full_name;
    }

    /** @return list<string> */
    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'email'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Applicant')
                ->columns(2)
                ->components([
                    TextInput::make('first_name')->required()->maxLength(100),
                    TextInput::make('last_name')->required()->maxLength(100),
                    TextInput::make('email')->email()->required(),
                    TextInput::make('phone')->tel(),
                ]),
            Section::make('Application')
                ->columns(2)
                ->components([
                    Select::make('requisition_id')->label('Role')
                        ->relationship('requisition', 'title')
                        ->searchable()->preload()
                        ->required(),
                    Select::make('source')
                        ->options(['careers' => 'Careers site', 'referral' => 'Referral', 'manual' => 'Manual'])
                        ->nullable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('full_name')->label('Name')->state(fn (Applicant $r) => $r->full_name),
                TextColumn::make('email'),
                TextColumn::make('requisition.title')->label('Role'),
                TextColumn::make('status')->badge(),
                TextColumn::make('source')->placeholder('—'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ApplicantResource\Pages\ListApplicants::route('/'),
        ];
    }
}
