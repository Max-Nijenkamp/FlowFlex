<?php

use App\Models\Marketing\OpenRole;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function makeRole(array $attrs = []): OpenRole
{
    static $counter = 0;
    $counter++;

    return OpenRole::create(array_merge([
        'title'            => 'Test Role ' . $counter,
        'slug'             => 'test-role-' . $counter,
        'department'       => 'Engineering',
        'location'         => 'Remote',
        'type'             => 'full-time',
        'about_role'       => 'About this role.',
        'responsibilities' => 'Key responsibilities.',
        'requirements'     => 'Required skills.',
        'how_to_apply'     => 'Send your CV.',
        'status'           => 'open',
    ], $attrs));
}

it('scopeOpen returns only roles with status open', function () {
    makeRole(['title' => 'Senior Full-Stack Engineer', 'slug' => 'senior-full-stack-engineer', 'status' => 'open']);
    makeRole(['title' => 'Junior Designer', 'slug' => 'junior-designer', 'status' => 'closed']);

    expect(OpenRole::open()->count())->toBe(1);
    expect(OpenRole::open()->first()->title)->toBe('Senior Full-Stack Engineer');
});

it('scopeOpen excludes closed roles', function () {
    makeRole(['title' => 'Closed Role', 'slug' => 'closed-role', 'status' => 'closed']);

    expect(OpenRole::open()->count())->toBe(0);
});

it('scopeOpen returns multiple open roles', function () {
    makeRole(['title' => 'Engineer A', 'slug' => 'engineer-a', 'status' => 'open']);
    makeRole(['title' => 'Engineer B', 'slug' => 'engineer-b', 'status' => 'open']);
    makeRole(['title' => 'Closed Manager', 'slug' => 'closed-manager', 'status' => 'closed']);

    expect(OpenRole::open()->count())->toBe(2);
});

it('can be soft deleted', function () {
    $role = makeRole(['title' => 'Delete This Role', 'slug' => 'delete-this-role']);

    $role->delete();

    expect(OpenRole::find($role->id))->toBeNull();
    expect(OpenRole::withTrashed()->find($role->id))->not->toBeNull();
});

it('uses ULID as primary key', function () {
    $role = makeRole(['title' => 'ULID Test Role', 'slug' => 'ulid-test-role']);

    expect(strlen($role->id))->toBe(26);
});
