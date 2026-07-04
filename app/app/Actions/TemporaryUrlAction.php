<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Media;
use App\Support\Services\CompanyContext;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\URL;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * 1-hour pre-signed download URL for a media row (core.file-storage/
 * upload-security). Ownership enforced here AND again in the controller.
 */
class TemporaryUrlAction
{
    use AsAction;

    public function handle(Media $media): string
    {
        if ($media->company_id !== app(CompanyContext::class)->currentId()) {
            throw new AuthorizationException('This file belongs to another workspace.');
        }

        return URL::temporarySignedRoute('media.download', now()->addHour(), ['media' => $media->id]);
    }
}
