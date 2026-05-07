<?php

namespace App\Filament\Admin\Resources\Marketing;

use App\Filament\Admin\Enums\NavigationGroup;
use App\Filament\Admin\Resources\Marketing\DemoRequestResource\Pages\EditDemoRequest;
use App\Filament\Admin\Resources\Marketing\DemoRequestResource\Pages\ListDemoRequests;
use App\Models\Marketing\DemoRequest;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DemoRequestResource extends Resource
{
    protected static ?string $model = DemoRequest::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-plus';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::MarketingContent;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::MarketingContent->label();
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.demo_requests.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.demo_requests.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('admin.resources.demo_requests.sections.crm'))
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->options([
                            'new'            => 'New',
                            'contacted'      => 'Contacted',
                            'demo_scheduled' => 'Demo Scheduled',
                            'demo_done'      => 'Demo Done',
                            'converted'      => 'Converted',
                            'lost'           => 'Lost',
                        ])
                        ->required(),

                    TextInput::make('assigned_to')
                        ->label('Assigned To')
                        ->maxLength(255),

                    DateTimePicker::make('scheduled_at')
                        ->label('Demo Scheduled At')
                        ->columnSpanFull(),

                    Textarea::make('notes_internal')
                        ->label('Internal Notes')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),

            Section::make(__('admin.resources.demo_requests.sections.lead_information'))
                ->columns(2)
                ->schema([
                    TextInput::make('first_name')->disabled(),
                    TextInput::make('last_name')->disabled(),
                    TextInput::make('email')->disabled(),
                    TextInput::make('company_name')->disabled(),
                    TextInput::make('company_size')->disabled(),
                    TextInput::make('heard_from')->disabled(),
                    TextInput::make('phone')->disabled(),
                    Textarea::make('notes')->disabled()->columnSpanFull(),
                ]),

            Section::make(__('admin.resources.demo_requests.sections.utm_tracking'))
                ->columns(3)
                ->collapsed()
                ->schema([
                    TextInput::make('utm_source')->disabled(),
                    TextInput::make('utm_medium')->disabled(),
                    TextInput::make('utm_campaign')->disabled(),
                    TextInput::make('utm_content')->disabled(),
                    TextInput::make('utm_term')->disabled(),
                    TextInput::make('ip_address')->disabled(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new'            => 'warning',
                        'contacted'      => 'info',
                        'demo_scheduled' => 'primary',
                        'demo_done'      => 'success',
                        'converted'      => 'success',
                        'lost'           => 'danger',
                        default          => 'gray',
                    }),

                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name']),

                TextColumn::make('company_name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('company_size')
                    ->label('Size')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'new'            => 'New',
                        'contacted'      => 'Contacted',
                        'demo_scheduled' => 'Demo Scheduled',
                        'demo_done'      => 'Demo Done',
                        'converted'      => 'Converted',
                        'lost'           => 'Lost',
                    ]),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDemoRequests::route('/'),
            'edit'  => EditDemoRequest::route('/{record}/edit'),
        ];
    }
}
