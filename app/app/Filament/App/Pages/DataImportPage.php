<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\Core\ImportJob;
use App\Services\Core\DataImportService;
use App\Support\Services\CompanyContext;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;

class DataImportPage extends Page
{
    use WithFileUploads;

    protected static ?string $slug = 'data-import';

    public string $entityType = '';

    #[Validate(['nullable', 'file', 'mimes:csv,txt', 'max:10240'])]
    public $csvFile = null;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-arrow-up-tray';
    }

    public static function getNavigationLabel(): string
    {
        return 'Data Import';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Tools';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function canAccess(): bool
    {
        return auth()->check()
            && app(CompanyContext::class)->hasCompany();
    }

    public function getTitle(): string
    {
        return 'Data Import';
    }

    public function getView(): string
    {
        return 'filament.app.pages.data-import';
    }

    public function getEntityOptions(): array
    {
        return [
            'employees' => 'Employees',
            'projects'  => 'Projects',
            'tasks'     => 'Tasks',
        ];
    }

    public function handleImport(): void
    {
        $this->validate([
            'entityType' => ['required', 'string', 'in:employees,projects,tasks'],
            'csvFile'    => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ]);

        $companyId = app(CompanyContext::class)->currentId();

        $storedPath = $this->csvFile->store("imports/{$companyId}", 'local');

        $absolutePath = Storage::disk('local')->path($storedPath);

        $rows = $this->parseCsv($absolutePath);

        if (empty($rows)) {
            Notification::make()
                ->title('The CSV file is empty or could not be parsed.')
                ->danger()
                ->send();

            return;
        }

        $service = app(DataImportService::class);
        $job     = $service->createJob($this->entityType, $storedPath);
        $service->parseAndStoreRows($job, $rows);

        $this->csvFile    = null;
        $this->entityType = '';

        Notification::make()
            ->title("Import job created (ID: {$job->id})")
            ->body(count($rows) . ' rows queued for processing.')
            ->success()
            ->send();
    }

    public function getRecentJobs(): Collection
    {
        $companyId = app(CompanyContext::class)->currentId();

        return ImportJob::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->latest()
            ->limit(10)
            ->get();
    }

    private function parseCsv(string $absolutePath): array
    {
        $handle = fopen($absolutePath, 'r');

        if ($handle === false) {
            return [];
        }

        $headers = fgetcsv($handle);

        if ($headers === false || empty($headers)) {
            fclose($handle);
            return [];
        }

        $rows = [];

        while (($line = fgetcsv($handle)) !== false) {
            if (count($line) === count($headers)) {
                $rows[] = array_combine($headers, $line);
            }
        }

        fclose($handle);

        return $rows;
    }
}
