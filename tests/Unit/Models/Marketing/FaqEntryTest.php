<?php

use App\Models\Marketing\FaqEntry;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('scopeForContext filters FAQ entries by context', function () {
    FaqEntry::create([
        'question'     => 'What is FlowFlex?',
        'answer'       => 'FlowFlex is a modular HR SaaS platform.',
        'context'      => 'general',
        'is_published' => true,
        'display_order' => 1,
    ]);

    FaqEntry::create([
        'question'     => 'How much does FlowFlex cost?',
        'answer'       => 'Pricing depends on the modules you activate.',
        'context'      => 'pricing',
        'is_published' => true,
        'display_order' => 1,
    ]);

    FaqEntry::create([
        'question'     => 'Is my data secure?',
        'answer'       => 'Yes, all data is encrypted at rest and in transit.',
        'context'      => 'security',
        'is_published' => true,
        'display_order' => 2,
    ]);

    expect(FaqEntry::forContext('general')->count())->toBe(1);
    expect(FaqEntry::forContext('pricing')->count())->toBe(1);
    expect(FaqEntry::forContext('security')->count())->toBe(1);
});

it('scopeForContext returns only entries matching the specified context', function () {
    FaqEntry::create([
        'question'     => 'General question one',
        'answer'       => 'General answer one.',
        'context'      => 'general',
        'is_published' => true,
        'display_order' => 1,
    ]);

    FaqEntry::create([
        'question'     => 'General question two',
        'answer'       => 'General answer two.',
        'context'      => 'general',
        'is_published' => true,
        'display_order' => 2,
    ]);

    FaqEntry::create([
        'question'     => 'Pricing question',
        'answer'       => 'Pricing answer.',
        'context'      => 'pricing',
        'is_published' => true,
        'display_order' => 1,
    ]);

    $generalEntries = FaqEntry::forContext('general')->get();

    expect($generalEntries)->toHaveCount(2);
    expect($generalEntries->pluck('context')->unique()->values()->all())->toBe(['general']);
});

it('scopeForContext returns empty collection when no entries match', function () {
    FaqEntry::create([
        'question'     => 'General question',
        'answer'       => 'General answer.',
        'context'      => 'general',
        'is_published' => true,
        'display_order' => 1,
    ]);

    expect(FaqEntry::forContext('nonexistent')->count())->toBe(0);
});

it('can be soft deleted', function () {
    $entry = FaqEntry::create([
        'question'     => 'Soft delete test',
        'answer'       => 'This entry will be deleted.',
        'context'      => 'general',
        'is_published' => true,
        'display_order' => 1,
    ]);

    $entry->delete();

    expect(FaqEntry::find($entry->id))->toBeNull();
    expect(FaqEntry::withTrashed()->find($entry->id))->not->toBeNull();
});

it('is_published is cast to boolean', function () {
    $entry = FaqEntry::create([
        'question'     => 'Cast test',
        'answer'       => 'Testing the boolean cast.',
        'context'      => 'general',
        'is_published' => true,
        'display_order' => 1,
    ]);

    expect($entry->is_published)->toBeBool();
    expect($entry->is_published)->toBeTrue();
});
