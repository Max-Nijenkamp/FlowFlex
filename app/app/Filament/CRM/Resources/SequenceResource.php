<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\CRM\Sequence;
use BackedEnum;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class SequenceResource extends Resource
{
    protected static ?string $model = Sequence::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static string|UnitEnum|null $navigationGroup = 'Outreach';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('crm.sequences.view-any')
            && app(BillingServiceInterface::class)->hasModule('crm.sequences');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Sequence')
                ->columns(2)
                ->components([
                    TextInput::make('name')->required()->maxLength(120),
                    Toggle::make('is_active')->label('Active')->default(true)->inline(false),
                    Hidden::make('owner_id')->default(fn () => Auth::guard('web')->id()),
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
                TextColumn::make('trigger_type')->badge(),
                TextColumn::make('steps_count')->counts('steps')->label('Steps'),
                TextColumn::make('enrolments_count')->counts('enrolments')->label('Enrolled'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => SequenceResource\Pages\ListSequences::route('/'),
        ];
    }
}
