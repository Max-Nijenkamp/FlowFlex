<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Models\HR\Employee;
use App\Models\HR\Feedback;
use App\Models\HR\ReviewGoal;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'Performance';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.feedback.view-any')
            && app(BillingServiceInterface::class)->hasModule('hr.feedback');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Who')
                ->columns(2)
                ->components([
                    Select::make('from_employee_id')->label('From')
                        ->options(fn () => Employee::query()->get()->pluck('full_name', 'id'))
                        ->searchable()
                        ->required(),
                    Select::make('to_employee_id')->label('To')
                        ->options(fn () => Employee::query()->get()->pluck('full_name', 'id'))
                        ->searchable()
                        ->required()
                        ->different('from_employee_id'),
                ]),
            Section::make('Feedback')
                ->columns(2)
                ->components([
                    Select::make('type')
                        ->options(['praise' => 'Praise', 'constructive' => 'Constructive', 'coaching-note' => 'Coaching note'])
                        ->required(),
                    Select::make('visibility')
                        ->options(['public' => 'Public', 'private' => 'Private', 'manager-chain' => 'Manager chain'])
                        ->required(),
                    Textarea::make('message')->required()->maxLength(2000)->columnSpanFull(),
                    Select::make('related_goal_id')->label('Related goal')
                        ->options(fn () => ReviewGoal::query()->pluck('title', 'id'))
                        ->searchable()
                        ->nullable()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('type')->badge(),
                TextColumn::make('message')->limit(60),
                TextColumn::make('visibility')->badge(),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => FeedbackResource\Pages\ListFeedback::route('/'),
        ];
    }
}
