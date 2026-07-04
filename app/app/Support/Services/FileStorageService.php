<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Settings\CompanyBusinessSettings;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mime\MimeTypes;

/**
 * Upload security gate (core.file-storage/upload-security): extension
 * denylist, MIME/extension agreement, per-company size cap. Every upload
 * surface calls validateUpload() before handing the file to media-library.
 */
class FileStorageService
{
    /** @var list<string> */
    public const FORBIDDEN_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'phtml', 'phar',
        'exe', 'com', 'bat', 'cmd', 'sh', 'bash',
        'js', 'mjs', 'vbs', 'ps1', 'dll', 'jar', 'svgz',
    ];

    public function validateUpload(UploadedFile $file): void
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, self::FORBIDDEN_EXTENSIONS, true)) {
            throw ValidationException::withMessages([
                'file' => "Files of type .{$extension} are not allowed.",
            ]);
        }

        $mime = $file->getMimeType();
        $allowedExtensions = $mime === null ? [] : MimeTypes::getDefault()->getExtensions($mime);

        if ($extension !== '' && $allowedExtensions !== [] && ! in_array($extension, $allowedExtensions, true)) {
            throw ValidationException::withMessages([
                'file' => "The file's content ({$mime}) does not match its .{$extension} extension.",
            ]);
        }

        $maxMb = app(CompanyBusinessSettings::class)->max_upload_mb;

        if ($file->getSize() > $maxMb * 1024 * 1024) {
            throw ValidationException::withMessages([
                'file' => "Files may be at most {$maxMb} MB for your workspace.",
            ]);
        }
    }
}
