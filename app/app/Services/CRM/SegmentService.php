<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Exceptions\CRM\InvalidSegmentConditionException;
use App\Models\CRM\Contact;
use App\Models\CRM\Segment;
use Illuminate\Database\Eloquent\Builder;

class SegmentService
{
    private const array FIELDS = ['lifecycle_stage', 'source', 'job_title', 'owner_id', 'email', 'custom_fields'];

    private const array OPERATORS = ['equals', 'not-equals', 'contains', 'is-set', 'is-not-set'];

    /**
     * THE single audience API. Dynamic: conditions resolved query-time
     * (CompanyScope inherent — never materialised). Static: membership join.
     *
     * @return Builder<Contact>
     */
    public function contacts(string $segmentId): Builder
    {
        $segment = Segment::query()->findOrFail($segmentId);

        if ($segment->type === 'static') {
            return Contact::query()->whereHas(
                'segmentMemberships',
                fn ($q) => $q->where('segment_id', $segment->id),
            );
        }

        return $this->applyConditions(Contact::query(), $segment->conditions ?? ['logic' => 'and', 'rules' => []]);
    }

    /** @param array{logic: string, rules: array<array{field: string, operator: string, value?: mixed}>} $conditions */
    public function preview(array $conditions): int
    {
        return $this->applyConditions(Contact::query(), $conditions)->count();
    }

    public function overlap(string $segmentA, string $segmentB): int
    {
        $idsA = $this->contacts($segmentA)->pluck('id');

        return $this->contacts($segmentB)->whereIn('id', $idsA)->count();
    }

    /** Validates every rule at save time — unknown field/operator rejected. */
    public function validateConditions(array $conditions): void
    {
        foreach ($conditions['rules'] ?? [] as $rule) {
            if (! in_array($rule['field'] ?? '', self::FIELDS, true)
                && ! str_starts_with($rule['field'] ?? '', 'custom_fields.')) {
                throw new InvalidSegmentConditionException("Unknown field [{$rule['field']}].");
            }
            if (! in_array($rule['operator'] ?? '', self::OPERATORS, true)) {
                throw new InvalidSegmentConditionException("Unknown operator [{$rule['operator']}].");
            }
        }
    }

    /**
     * @param  Builder<Contact>  $query
     * @return Builder<Contact>
     */
    private function applyConditions(Builder $query, array $conditions): Builder
    {
        $logic = ($conditions['logic'] ?? 'and') === 'or' ? 'or' : 'and';
        $rules = $conditions['rules'] ?? [];

        return $query->where(function (Builder $group) use ($rules, $logic): void {
            foreach ($rules as $rule) {
                $method = $logic === 'or' ? 'orWhere' : 'where';
                $field = $rule['field'];
                $value = $rule['value'] ?? null;

                if (str_starts_with($field, 'custom_fields.')) {
                    $key = substr($field, strlen('custom_fields.'));
                    $field = "custom_fields->{$key}";
                }

                match ($rule['operator']) {
                    'equals' => $group->{$method}($field, '=', $value),
                    'not-equals' => $group->{$method}($field, '!=', $value),
                    'contains' => $group->{$method}($field, 'like', "%{$value}%"),
                    'is-set' => $group->{$method.'NotNull'}($field),
                    'is-not-set' => $group->{$method.'Null'}($field),
                    default => throw new InvalidSegmentConditionException("Unknown operator [{$rule['operator']}]."),
                };
            }
        });
    }
}
