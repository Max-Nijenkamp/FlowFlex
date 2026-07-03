<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Test-only tenant model — no production tenant models exist until the
 * domain phases; this exercises the full BelongsToCompany query path.
 */
class TestItem extends Model
{
    use BelongsToCompany;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'test_items';

    protected $guarded = [];

    public static function migrate(): void
    {
        if (Schema::hasTable('test_items')) {
            return;
        }

        Schema::create('test_items', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->index();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
