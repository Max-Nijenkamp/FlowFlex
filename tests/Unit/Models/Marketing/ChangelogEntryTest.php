<?php

use App\Models\Marketing\ChangelogEntry;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('scopePublished returns only entries where is_published is true', function () {
    ChangelogEntry::create([
        'title'        => 'New HR Module Features',
        'type'         => 'feature',
        'body'         => 'We shipped several improvements to the HR module.',
        'is_published' => true,
        'published_at' => now()->subDays(5),
    ]);

    ChangelogEntry::create([
        'title'        => 'Upcoming Feature (Draft)',
        'type'         => 'feature',
        'body'         => 'Coming soon...',
        'is_published' => false,
        'published_at' => null,
    ]);

    expect(ChangelogEntry::published()->count())->toBe(1);
    expect(ChangelogEntry::published()->first()->title)->toBe('New HR Module Features');
});

it('scopePublished excludes entries where is_published is false', function () {
    ChangelogEntry::create([
        'title'        => 'Unpublished Entry',
        'type'         => 'improvement',
        'body'         => 'Not yet live.',
        'is_published' => false,
        'published_at' => now()->subDay(),
    ]);

    expect(ChangelogEntry::published()->count())->toBe(0);
});

it('scopePublished returns multiple published entries', function () {
    ChangelogEntry::create([
        'title'        => 'Feature A',
        'type'         => 'feature',
        'body'         => 'First published entry.',
        'is_published' => true,
        'published_at' => now()->subDays(10),
    ]);

    ChangelogEntry::create([
        'title'        => 'Bug Fix B',
        'type'         => 'fix',
        'body'         => 'Second published entry.',
        'is_published' => true,
        'published_at' => now()->subDays(5),
    ]);

    ChangelogEntry::create([
        'title'        => 'Draft C',
        'type'         => 'improvement',
        'body'         => 'Not published yet.',
        'is_published' => false,
        'published_at' => null,
    ]);

    expect(ChangelogEntry::published()->count())->toBe(2);
});

it('is_published is cast to boolean', function () {
    $entry = ChangelogEntry::create([
        'title'        => 'Cast Test',
        'type'         => 'feature',
        'body'         => 'Testing boolean cast.',
        'is_published' => true,
        'published_at' => now()->subDay(),
    ]);

    expect($entry->is_published)->toBeBool();
    expect($entry->is_published)->toBeTrue();
});

it('published_at is cast to datetime', function () {
    $publishedAt = now()->subDays(3)->startOfDay();

    $entry = ChangelogEntry::create([
        'title'        => 'Datetime Cast Test',
        'type'         => 'feature',
        'body'         => 'Testing datetime cast.',
        'is_published' => true,
        'published_at' => $publishedAt,
    ]);

    expect($entry->published_at)->toBeInstanceOf(\DateTimeInterface::class);
});

it('can be soft deleted', function () {
    $entry = ChangelogEntry::create([
        'title'        => 'Delete This Entry',
        'type'         => 'fix',
        'body'         => 'This entry will be soft deleted.',
        'is_published' => true,
        'published_at' => now()->subDay(),
    ]);

    $entry->delete();

    expect(ChangelogEntry::find($entry->id))->toBeNull();
    expect(ChangelogEntry::withTrashed()->find($entry->id))->not->toBeNull();
});

it('uses ULID as primary key', function () {
    $entry = ChangelogEntry::create([
        'title'        => 'ULID Check',
        'type'         => 'feature',
        'body'         => 'Checking ULID primary key.',
        'is_published' => true,
        'published_at' => now()->subDay(),
    ]);

    expect(strlen($entry->id))->toBe(26);
});
