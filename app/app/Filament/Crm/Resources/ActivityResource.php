<?php

declare(strict_types=1);

namespace App\Filament\Crm\Resources;

use App\Models\Crm\Account;
use App\Models\Crm\Activity;
use App\Models\Crm\Contact;
use App\Models\Crm\Deal;
use App\Models\User;
use App\Services\BillingService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/**
 * Calls, emails, meetings, tasks and notes (crm.activities). A task
 * needs a due date; every activity links to at least one of contact /
 * deal / account.
 */
class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-phone';

    protected static string|\UnitEnum|null $navigationGroup = 'Activities';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'activity';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('crm.activities.view-any')
            && app(BillingService::class)->hasModule('crm.activities');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Activity')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    Select::make('type')
                        ->options([
                            'call' => 'Call', 'email' => 'Email', 'meeting' => 'Meeting',
                            'task' => 'Task', 'note' => 'Note',
                        ])
                        ->live()
                        ->required(),
                    TextInput::make('subject')->required()->maxLength(255),
                    Textarea::make('description')->rows(3)->columnSpanFull()->maxLength(5000),
                    Select::make('contact_id')
                        ->label('Contact')
                        ->options(fn (): array => Contact::query()->get()->sortBy('last_name')->mapWithKeys(
                            fn (Contact $contact): array => [$contact->id => $contact->full_name],
                        )->all())
                        ->searchable()
                        ->placeholder('None')
                        ->requiredWithoutAll(['deal_id', 'account_id'])
                        ->validationMessages(['required_without_all' => 'Link the activity to a contact, deal, or account.']),
                    Select::make('deal_id')
                        ->label('Deal')
                        ->options(fn (): array => Deal::query()->orderByDesc('created_at')->pluck('name', 'id')->all())
                        ->searchable()
                        ->placeholder('None'),
                    Select::make('account_id')
                        ->label('Account')
                        ->options(fn (): array => Account::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->placeholder('None'),
                    DateTimePicker::make('activity_date')
                        ->label('When')
                        ->native(false)
                        ->default(now())
                        ->required(),
                    TextInput::make('duration_minutes')->numeric()->minValue(1)->label('Duration (min)'),
                    DateTimePicker::make('due_at')
                        ->label('Due')
                        ->native(false)
                        ->visible(fn (callable $get): bool => $get('type') === 'task')
                        ->requiredIf('type', 'task')
                        ->validationMessages(['required_if' => 'Tasks need a due date.']),
                    TextInput::make('outcome')->maxLength(160),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('subject')->searchable()->limit(50),
                TextColumn::make('contact.full_name')->label('Contact')->placeholder('—'),
                TextColumn::make('deal.name')->label('Deal')->placeholder('—')->limit(30),
                TextColumn::make('activity_date')->label('When')->dateTime('d M Y · H:i')->sortable(),
                TextColumn::make('taskStatus')
                    ->label('Status')
                    ->badge()
                    ->state(fn (Activity $record): string => match (true) {
                        $record->type !== 'task' => 'Logged',
                        $record->is_complete => 'Done',
                        $record->isOverdue() => 'Overdue',
                        default => 'Open',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Done' => 'success', 'Overdue' => 'danger', 'Open' => 'warning', default => 'gray',
                    }),
                TextColumn::make('owner.full_name')->label('Owner'),
            ])
            ->defaultSort('activity_date', 'desc')
            ->filters([
                SelectFilter::make('type')->options([
                    'call' => 'Call', 'email' => 'Email', 'meeting' => 'Meeting', 'task' => 'Task', 'note' => 'Note',
                ]),
                SelectFilter::make('owner_id')
                    ->label('Owner')
                    ->options(fn (): array => User::query()->get()->mapWithKeys(
                        fn (User $user): array => [$user->id => $user->full_name],
                    )->all()),
                TernaryFilter::make('is_complete')->label('Completed'),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('complete')
                        ->label('Mark done')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Activity $record): bool => $record->type === 'task' && ! $record->is_complete)
                        ->action(function (Activity $record): void {
                            $record->update(['is_complete' => true, 'outcome' => $record->outcome ?? 'done']);
                            Notification::make()->success()->title('Task completed')->send();
                        }),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->emptyStateHeading('No activity yet')
            ->emptyStateDescription('Log a call, plan a task, or note a meeting — the timeline builds itself.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ActivityResource\Pages\ListActivities::route('/'),
            'create' => ActivityResource\Pages\CreateActivity::route('/create'),
            'edit' => ActivityResource\Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
