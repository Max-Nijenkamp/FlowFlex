<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources;

use App\Contracts\BillingServiceInterface;
use App\Exceptions\HR\EmptyCycleException;
use App\Models\HR\ReviewCycle;
use App\Services\HR\PerformanceService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ReviewCycleResource extends Resource
{
    protected static ?string $model = ReviewCycle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static string|UnitEnum|null $navigationGroup = 'Performance';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.performance.view-any')
            && app(BillingServiceInterface::class)->hasModule('hr.performance');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('type')->badge(),
                TextColumn::make('status')->badge(),
                TextColumn::make('reviews_count')->counts('reviews')->label('Reviews'),
            ])
            ->recordActions([
                Action::make('activate')
                    ->icon(Heroicon::OutlinedPlay)
                    ->visible(fn (ReviewCycle $r) => (string) $r->status === 'draft'
                        && Auth::guard('web')->user()->can('hr.performance.manage-cycles'))
                    ->requiresConfirmation()
                    ->action(function (ReviewCycle $record): void {
                        try {
                            app(PerformanceService::class)->activateCycle($record->id);
                            Notification::make()->success()->title('Cycle activated — reviews generated')->send();
                        } catch (EmptyCycleException $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ReviewCycleResource\Pages\ListReviewCycles::route('/'),
        ];
    }
}
