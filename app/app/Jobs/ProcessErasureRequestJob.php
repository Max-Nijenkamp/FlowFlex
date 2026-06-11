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

/**
 * Erasure cascade per architecture/data-lifecycle: each registered table
 * declares its rule — `anonymise` (PII fields nulled/scrambled, row kept,
 * e.g. legal-hold financial records reference) or `delete` (hard delete).
 * Anonymise writes are idempotent — safe to re-run.
 */
class ProcessErasureRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?string $company_id = null;

    public function __construct(
        public readonly string $requestId,
    ) {
        $this->onQueue('default');

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

        foreach ($registry->tables() as $table => $config) {
            $emailColumn = (string) ($config['email_column'] ?? 'email');
            $rule = (string) ($config['erasure'] ?? 'anonymise');

            $query = DB::table($table)
                ->where('company_id', $request->company_id)
                ->where($emailColumn, $request->subject_email);

            if ($rule === 'delete') {
                $query->delete();

                continue;
            }

            // anonymise: scramble declared PII fields, keep the row.
            $fields = (array) ($config['fields'] ?? []);
            $updates = [];
            foreach ($fields as $field) {
                $updates[$field] = $field === $emailColumn
                    ? 'erased-'.substr(hash('sha256', $request->subject_email), 0, 12).'@erased.invalid'
                    : '[erased]';
            }

            if ($updates !== []) {
                $query->update($updates);
            }
        }

        $request->forceFill(['completed_at' => now()])->save();
        $request->status->transitionTo(Completed::class);
    }
}
