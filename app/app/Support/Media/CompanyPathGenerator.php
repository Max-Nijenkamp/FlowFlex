<?php

declare(strict_types=1);

namespace App\Support\Media;

use App\Models\Media as CompanyMedia;
use RuntimeException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

/**
 * Every media path lives under companies/{company_id}/ (core.file-storage/
 * path-generator) — originals, conversions and responsive images alike.
 * Fails closed: a media row without company_id never produces a path.
 */
class CompanyPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->basePath($media).'/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->basePath($media).'/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->basePath($media).'/responsive-images/';
    }

    private function basePath(Media $media): string
    {
        $companyId = $media instanceof CompanyMedia ? $media->company_id : null;

        if (blank($companyId)) {
            throw new RuntimeException('Refusing to build a media path without a company_id — tenant isolation would break.');
        }

        $table = $media->model_type !== '' ? (new $media->model_type)->getTable() : 'misc';

        return "companies/{$companyId}/{$table}/{$media->model_id}";
    }
}
