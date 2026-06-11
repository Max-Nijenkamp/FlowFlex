<?php

declare(strict_types=1);

namespace App\Support\Media;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

/**
 * Every stored file lives under companies/{company_id}/ — including
 * conversions and responsive images. Tenant isolation at the storage layer.
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
        $owner = $media->getRelationValue('model');
        $companyId = $media->getCustomProperty('company_id')
            ?? $owner->company_id
            ?? 'platform';

        $table = $owner?->getTable() ?? 'misc';

        return "companies/{$companyId}/{$table}/{$media->model_id}";
    }
}
