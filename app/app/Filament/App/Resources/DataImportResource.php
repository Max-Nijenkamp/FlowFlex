<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Actions\StartImportAction;
use App\Contracts\BillingServiceInterface;
use App\Data\CreateImportData;
use App\Models\DataImport;
use App\Support\Import\ImporterRegistry;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use UnitEnum;

/**
 * Import history. The upload + mapping wizard lands with the first registered
 * importer (hr.profiles / crm.contacts) — until then the registry is empty
 * and there is nothing to import into.
 */
class DataImportResource extends Resource
{
    protected static ?string $model = DataImport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $modelLabel = 'data import';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('core.import.view-any')
            && app(BillingServiceInterface::class)->hasModule('core.import');
    }

    public static function canCreate(): bool
    {
        return false; // wizard arrives with the first importer target
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading() // perceived-performance: paint page, stream rows
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->emptyStateHeading('Nothing imported yet')
            ->emptyStateDescription('Bring a CSV with a header row — employees and contacts import in one go, and every row that fails tells you why.')
            ->headerActions([
                Action::make('new_import')
                    ->label('New import')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->visible(fn (): bool => Auth::guard('web')->user()->can('core.import.create')
                        && app(ImporterRegistry::class)->available() !== [])
                    ->schema([
                        Select::make('target')
                            ->label('Import into')
                            ->options(fn (): array => collect(app(ImporterRegistry::class)->available())
                                ->keys()
                                ->mapWithKeys(fn (string $key): array => [$key => str(str_replace('.', ' — ', $key))->headline()->toString()])
                                ->all())
                            ->required(),
                        FileUpload::make('file')
                            ->label('CSV file')
                            ->disk('local')
                            ->directory('imports')
                            ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                            ->storeFileNamesIn('original_filename')
                            ->required()
                            ->helperText('Header row required — column names must match the target field names; mismatches are reported per row.'),
                    ])
                    ->action(function (array $data): void {
                        $storedPath = $data['file'];
                        $handle = fopen(Storage::disk('local')->path($storedPath), 'r');
                        $headers = array_map('trim', str_getcsv((string) fgets($handle)));
                        fclose($handle);

                        try {
                            StartImportAction::run(new CreateImportData(
                                target: $data['target'],
                                stored_path: $storedPath,
                                filename: (string) ($data['original_filename'] ?? basename($storedPath)),
                                column_map: array_combine($headers, $headers) ?: [],
                            ));
                            Notification::make()->success()
                                ->title('Import started')
                                ->body('Rows process in the background — failures land in the Errors column with a reason.')
                                ->send();
                        } catch (ValidationException $e) {
                            Notification::make()->danger()
                                ->title('Import not started')
                                ->body(implode(' ', collect($e->errors())->flatten()->all()))
                                ->send();
                        }
                    }),
            ])
            ->columns([
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('target')->badge(),
                TextColumn::make('filename'),
                TextColumn::make('status')->badge()
                    ->color(fn ($state): string => match ((string) $state) {
                        'complete' => 'success',
                        'failed' => 'danger',
                        default => 'info',
                    }),
                TextColumn::make('success_rows')->label('OK'),
                TextColumn::make('error_rows')->label('Errors'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => DataImportResource\Pages\ListDataImports::route('/'),
        ];
    }
}
