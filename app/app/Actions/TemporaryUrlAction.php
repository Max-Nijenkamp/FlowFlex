<?php

declare(strict_types=1);

namespace App\Actions;

use App\Support\Services\CompanyContext;
use Illuminate\Auth\Access\AuthorizationException;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TemporaryUrlAction
{
    use AsAction;

    /**
     * Pre-signed URL (1h TTL). Throws unless the media belongs to the current
     * company — company A can never resolve company B's files.
     */
    public function handle(Media $media): string
    {
        $owner = $media->getRelationValue('model');
        $ownerCompanyId = $media->getCustomProperty('company_id') ?? $owner->company_id ?? null;

        if ($ownerCompanyId !== app(CompanyContext::class)->current()->id) {
            throw new AuthorizationException('File belongs to another workspace.');
        }

        // Local disk has no temporary URLs — fall back to the regular URL in dev.
        try {
            return $media->getTemporaryUrl(now()->addHour());
        } catch (\RuntimeException) {
            return $media->getUrl();
        }
    }
}
