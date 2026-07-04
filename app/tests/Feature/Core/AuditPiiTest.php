<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Company;
use App\Support\Services\AuditLogger;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Support\TestItem;

/** Test-only model with encrypted casts + an explicit audit exclude list. */
class PiiProbeModel extends Model
{
    use BelongsToCompany;
    use HasUlids;
    use SoftDeletes;

    protected $table = 'pii_probes';

    protected $guarded = [];

    /** @var list<string> */
    public array $auditExclude = ['internal_note'];

    protected function casts(): array
    {
        return [
            'national_id' => 'encrypted',
            'iban' => 'encrypted',
        ];
    }

    public static function migrate(): void
    {
        if (Schema::hasTable('pii_probes')) {
            return;
        }

        Schema::create('pii_probes', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->index();
            $table->string('name');
            $table->text('national_id')->nullable();
            $table->text('iban')->nullable();
            $table->text('internal_note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}

beforeEach(function (): void {
    TestItem::migrate();
    PiiProbeModel::migrate();
    setCompany(Company::factory()->create());
});

test('encrypted-cast values are redacted even when absent from the exclude list', function () {
    $probe = PiiProbeModel::query()->create([
        'name' => 'Jan',
        'national_id' => '123456789',
        'iban' => 'NL91ABNA0417164300',
    ]);

    app(AuditLogger::class)->log('hr.updated', $probe, null, [
        'attributes' => ['name' => 'Jan', 'national_id' => '123456789', 'iban' => 'NL91ABNA0417164300'],
        'old' => ['national_id' => '987654321'],
    ]);

    $properties = json_encode(Activity::query()->sole()->properties);

    expect($properties)->not->toContain('123456789')
        ->not->toContain('NL91ABNA0417164300')
        ->not->toContain('987654321')
        ->toContain('national_id') // field NAME survives as a marker
        ->toContain('Jan');        // ordinary fields keep their values
});

test('the per-model auditExclude list is honored', function () {
    $probe = PiiProbeModel::query()->create(['name' => 'Jan', 'internal_note' => 'super secret']);

    app(AuditLogger::class)->log('hr.updated', $probe, null, [
        'attributes' => ['internal_note' => 'super secret'],
    ]);

    expect(json_encode(Activity::query()->sole()->properties))->not->toContain('super secret');
});

test('the fail-closed floor redacts sensitive keys on models with no configuration at all', function () {
    $item = TestItem::query()->create(['name' => 'Widget']);

    app(AuditLogger::class)->log('hr.updated', $item, null, [
        'attributes' => ['salary' => 54000, 'password' => 'hunter2', 'name' => 'Widget'],
    ]);

    $properties = json_encode(Activity::query()->sole()->properties);

    expect($properties)->not->toContain('54000')
        ->not->toContain('hunter2')
        ->toContain('Widget');
});

test('empty or PII-free properties pass through unchanged', function () {
    $item = TestItem::query()->create(['name' => 'Widget']);

    app(AuditLogger::class)->log('hr.created', $item, null, ['attributes' => ['name' => 'Widget']]);

    expect(Activity::query()->sole()->properties['attributes'])->toBe(['name' => 'Widget']);
});
