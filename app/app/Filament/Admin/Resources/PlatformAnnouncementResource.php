<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Events\Foundation\PlatformAnnouncementSent;
use App\Filament\Admin\Resources\PlatformAnnouncementResource\Pages\CreatePlatformAnnouncement;
use App\Filament\Admin\Resources\PlatformAnnouncementResource\Pages\EditPlatformAnnouncement;
use App\Filament\Admin\Resources\PlatformAnnouncementResource\Pages\ListPlatformAnnouncements;
use App\Jobs\Foundation\DispatchAnnouncementJob;
use App\Models\Company;
use App\Models\PlatformAnnouncement;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PlatformAnnouncementResource extends Resource
{
    protected static ?string $model = PlatformAnnouncement::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-megaphone';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Announcements';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Announcement Details')->columnSpanFull()->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Select::make('target')
                    ->options([
                        'all'     => 'All companies',
                        'company' => 'Specific company',
                    ])
                    ->required()
                    ->default('all')
                    ->live(),
                Select::make('target_value')
                    ->label('Target company')
                    ->options(fn () => Company::withoutGlobalScopes()->pluck('name', 'id'))
                    ->searchable()
                    ->visible(fn (Get $get) => $get('target') === 'company')
                    ->required(fn (Get $get) => $get('target') === 'company'),
                MarkdownEditor::make('body')
                    ->required()
                    ->columnSpanFull()
                    ->toolbarButtons([
                        'bold', 'italic', 'link', 'bulletList', 'orderedList', 'heading',
                    ]),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('target')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'all' ? 'primary' : 'warning'),
                TextColumn::make('target_value')
                    ->label('Target company')
                    ->default('—'),
                TextColumn::make('sent_at')
                    ->dateTime()
                    ->label('Sent at')
                    ->default('Draft'),
                TextColumn::make('creator.name')
                    ->label('Created by'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn (PlatformAnnouncement $record) => $record->isDraft()),
                Action::make('send')
                    ->label('Send')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (PlatformAnnouncement $record) => $record->isDraft())
                    ->action(function (PlatformAnnouncement $record): void {
                        $record->update(['sent_at' => now()]);
                        event(new PlatformAnnouncementSent($record));
                        DispatchAnnouncementJob::dispatch($record->id);
                        Notification::make()
                            ->title('Announcement queued for delivery')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPlatformAnnouncements::route('/'),
            'create' => CreatePlatformAnnouncement::route('/create'),
            'edit'   => EditPlatformAnnouncement::route('/{record}/edit'),
        ];
    }
}
