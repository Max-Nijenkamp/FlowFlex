<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\CRM\Segment;
use BackedEnum;
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

class SegmentResource extends Resource
{
    protected static ?string $model = Segment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFunnel;

    protected static string|UnitEnum|null $navigationGroup = 'Audience';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('crm.segments.view-any')
            && app(BillingServiceInterface::class)->hasModule('crm.segments');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Segment')
                ->columns(2)
                ->components([
                    TextInput::make('name')->required()->maxLength(120),
                    Select::make('type')
                        ->options(['dynamic' => 'Dynamic — keeps itself up to date', 'static' => 'Static — fixed member list'])
                        ->default('dynamic')
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
                TextColumn::make('name')->searchable(),
                TextColumn::make('type')->badge(),
                TextColumn::make('member_count')->label('Members'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => SegmentResource\Pages\ListSegments::route('/'),
        ];
    }
}
