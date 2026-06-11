<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\DsarRequest;
use App\States\DsarRequest\Completed;
use App\States\DsarRequest\InProgress;
use App\Support\Jobs\Middleware\WithCompanyContext;
use App\Support\Privacy\PersonalDataRegistry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Collects the subject's rows across all registered PII tables into one CSV
 * bundle. Re-runnable — regenerates and overwrites the result path.
 */
class ProcessAccessRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?string $company_id = null;

    public function __construct(
        public readonly string $requestId,
    ) {
        $this->onQueue('exports');

        $this->company_id = DsarRequest::query()->withoutGlobalScopes()
            ->whereKey($requestId)->value('company_id');
    }

    /** @return list<object> */
    public function middleware(): array
    {
        return [new WithCompanyContext];
    }

    public function handle(PersonalDataRegistry $registry): void
    {
        $request = DsarRequest::query()->withoutGlobalScopes()->findOrFail($this->requestId);

        if (! $request->status->equals(InProgress::class)) {
            $request->status->transitionTo(InProgress::class);
        }

        $sections = [];

        foreach ($registry->tables() as $table => $config) {
            $emailColumn = (string) ($config['email_column'] ?? 'email');
            $fields = (array) ($config['fields'] ?? ['*']);

            $rows = DB::table($table)
                ->where('company_id', $request->company_id)
                ->where($emailColumn, $request->subject_email)
                ->get($fields === ['*'] ? ['*'] : $fields);

            if ($rows->isEmpty()) {
                continue;
            }

            $header = implode(',', array_keys((array) $rows->first()));
            $lines = $rows->map(fn ($row) => implode(',', array_map(
                fn ($v) => '"'.str_replace('"', '""', (string) $v).'"',
                (array) $row,
            )))->implode("\n");

            $sections[] = "# {$table}\n{$header}\n{$lines}";
        }

        $path = "companies/{$request->company_id}/dsar/{$request->id}/export.csv";
        Storage::put($path, implode("\n\n", $sections) ?: 'No personal data found.');

        $request->forceFill(['result_path' => $path, 'completed_at' => now()])->save();
        $request->status->transitionTo(Completed::class);
    }
}
