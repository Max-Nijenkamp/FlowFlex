<?php

declare(strict_types=1);

namespace App\Http\Controllers\Media;

use App\Models\Media;
use App\Support\Services\CompanyContext;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MediaDownloadController
{
    public function __invoke(Request $request, Media $media): Response
    {
        abort_unless($request->hasValidSignature(), 403);
        abort_unless($media->company_id === app(CompanyContext::class)->currentId(), 403);

        return response()->download($media->getPath(), $media->file_name);
    }
}
