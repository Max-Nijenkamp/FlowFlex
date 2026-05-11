<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources;

use App\Filament\Projects\Resources\TimeEntryResource\Pages\CreateTimeEntry;
use App\Filament\Projects\Resources\TimeEntryResource\Pages\EditTimeEntry;
use App\Filament\Projects\Resources\TimeEntryResource\Pages\ListTimeEntries;
use App\Models\Projects\Project;
use App\Models\Projects\Task;
use App\Models\Projects\TimeEntry;
use App\Models\User;
use App\Support\Services\CompanyContext;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TimeEntryResource extends Resource
{
    protected static ?string $model = TimeEntry::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-clock';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Time';
    }

    public static function getNavigationLabel(): string
    {
        return 'Time Entries';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function canAccess(): bool
    {
        if (! auth()->check()) {
            return false;
        }
        $ctx = app(CompanyContext::class);
        if (! $ctx->hasCompany()) {
            return false;
        }

        return app(\App\Services\Core\BillingService::class)
            ->enforceModuleAccess($ctx->current(), 'projects.time');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Time Entry Details')->columnSpanFull()->schema([
                Select::make('user_id')
                    ->label('User')
                    ->options(fn () => User::withoutGlobalScopes()
                        ->where('company_id', app(CompanyContext::class)->currentId())
                        ->pluck('email', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('task_id')
                    ->label('Task')
                    ->options(fn () => Task::withoutGlobalScopes()
                        ->where('company_id', app(CompanyContext::class)->currentId())
                        ->pluck('title', 'id'))
                    ->searchable()
                    ->nullable(),
                Select::make('project_id')
                    ->label('Project')
                    ->options(fn () => Project::withoutGlobalScopes()
                        ->where('company_id', app(CompanyContext::class)->currentId())
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                DatePicker::make('date')->required(),
                TextInput::make('hours')
                    ->numeric()
                    ->suffix('hrs')
                    ->required(),
                Textarea::make('description')
                    ->nullable()
                    ->columnSpanFull(),
                Toggle::make('is_billable')
                    ->label('Billable'),
                TextInput::make('billing_rate')
                    ->numeric()
                    ->prefix('$')
                    ->nullable(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.email')
                    ->label('User')
                    ->searchable(),
                TextColumn::make('task.title')
                    ->label('Task')
                    ->placeholder('—'),
                TextColumn::make('project.name')
                    ->label('Project')
                    ->placeholder('—'),
                TextColumn::make('date')->date()->sortable(),
                TextColumn::make('hours')
                    ->suffix(' hrs'),
                IconColumn::make('is_billable')
                    ->label('Billable')
                    ->boolean(),
                TextColumn::make('approved_at')
                    ->label('Approved')
                    ->dateTime()
                    ->placeholder('Pending'),
            ])
            ->actions([
                EditAction::make(),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (TimeEntry $record) => $record->approved_at === null)
                    ->requiresConfirmation()
                    ->action(function (TimeEntry $record): void {
                        $record->update([
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()->title('Time entry approved')->success()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTimeEntries::route('/'),
            'create' => CreateTimeEntry::route('/create'),
            'edit'   => EditTimeEntry::route('/{record}/edit'),
        ];
    }
}
