<?php

namespace App\Filament\Admin\Resources\Marketing;

use App\Filament\Admin\Enums\NavigationGroup;
use App\Filament\Admin\Resources\Marketing\ContactSubmissionResource\Pages\EditContactSubmission;
use App\Filament\Admin\Resources\Marketing\ContactSubmissionResource\Pages\ListContactSubmissions;
use App\Models\Marketing\ContactSubmission;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactSubmissionResource extends Resource
{
    protected static ?string $model = ContactSubmission::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-inbox';

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::MarketingContent;

    protected static ?int $navigationSort = 12;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::MarketingContent->label();
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.contact_submissions.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.contact_submissions.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('admin.resources.contact_submissions.sections.status'))
                ->schema([
                    Select::make('status')
                        ->options([
                            'new'     => 'New',
                            'replied' => 'Replied',
                            'closed'  => 'Closed',
                        ])
                        ->required(),
                ]),

            Section::make(__('admin.resources.contact_submissions.sections.submission_details'))
                ->columns(2)
                ->schema([
                    TextInput::make('name')->disabled(),
                    TextInput::make('email')->disabled(),
                    TextInput::make('subject')->disabled(),
                    Textarea::make('message')->disabled()->rows(8)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->color('gray'),

                TextColumn::make('subject')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new'     => 'warning',
                        'replied' => 'info',
                        'closed'  => 'gray',
                        default   => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'new'     => 'New',
                        'replied' => 'Replied',
                        'closed'  => 'Closed',
                    ]),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactSubmissions::route('/'),
            'edit'  => EditContactSubmission::route('/{record}/edit'),
        ];
    }
}
