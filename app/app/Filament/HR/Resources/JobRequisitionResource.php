<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\HR\Department;
use App\Models\HR\JobRequisition;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Unique;
use UnitEnum;

class JobRequisitionResource extends Resource
{
    protected static ?string $model = JobRequisition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static string|UnitEnum|null $navigationGroup = 'Recruitment';

    protected static ?string $recordTitleAttribute = 'title';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.recruitment.view-any')
            && app(BillingServiceInterface::class)->hasModule('hr.recruitment');
    }

    /** @return list<string> */
    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'slug'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Role')
                ->columns(2)
                ->components([
                    TextInput::make('title')->required()->maxLength(150),
                    Select::make('department_id')->label('Department')
                        ->options(fn () => Department::query()->pluck('name', 'id'))
                        ->nullable(),
                    Select::make('employment_type')
                        ->options(['full-time' => 'Full-time', 'part-time' => 'Part-time', 'contractor' => 'Contractor'])
                        ->required(),
                    TextInput::make('headcount')->numeric()->integer()->minValue(1)->default(1)->required(),
                    Textarea::make('description')->required()->maxLength(10000)->columnSpanFull(),
                ]),
            Section::make('Publication')
                ->columns(2)
                ->components([
                    TextInput::make('slug')->required()->alphaDash()->maxLength(150)
                        ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule) => $rule->where('company_id', getPermissionsTeamId()))
                        ->helperText('Used in the public careers URL'),
                    DatePicker::make('open_date')->nullable(),
                    Select::make('status')
                        ->options(['draft' => 'Draft', 'open' => 'Open', 'closed' => 'Closed'])
                        ->default('draft')
                        ->required(),
                ]),
        ]);
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
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => JobRequisitionResource\Pages\ListJobRequisitions::route('/'),
        ];
    }
}
