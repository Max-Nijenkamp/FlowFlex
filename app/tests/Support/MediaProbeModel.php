<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/** Test-only tenant model that owns media (core.file-storage tests). */
class MediaProbeModel extends Model implements HasMedia
{
    use BelongsToCompany;
    use HasUlids;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $table = 'media_probes';

    protected $guarded = [];

    public static function migrate(): void
    {
        if (Schema::hasTable('media_probes')) {
            return;
        }

        Schema::create('media_probes', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->index();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
