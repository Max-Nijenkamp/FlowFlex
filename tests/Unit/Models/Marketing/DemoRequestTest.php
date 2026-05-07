<?php

use App\Models\Marketing\DemoRequest;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('getFullNameAttribute returns first and last name combined', function () {
    $demo = new DemoRequest([
        'first_name' => 'Sophie',
        'last_name'  => 'de Vries',
    ]);

    expect($demo->full_name)->toBe('Sophie de Vries');
});

it('getFullNameAttribute trims extra whitespace', function () {
    $demo = new DemoRequest([
        'first_name' => 'Sophie',
        'last_name'  => '',
    ]);

    expect($demo->full_name)->toBe('Sophie');
});

it('getFullNameAttribute works when only last_name is set', function () {
    $demo = new DemoRequest([
        'first_name' => '',
        'last_name'  => 'de Vries',
    ]);

    expect($demo->full_name)->toBe('de Vries');
});

it('can be created with all fields', function () {
    $demo = DemoRequest::create([
        'first_name'         => 'Tom',
        'last_name'          => 'Baker',
        'email'              => 'tom@example.com',
        'company_name'       => 'Baker Ltd',
        'company_size'       => '11-50',
        'modules_interested' => ['hr', 'projects'],
        'heard_from'         => 'Google',
        'notes'              => 'Interested in a full demo.',
        'phone'              => '+31 6 12345678',
        'ip_address'         => '127.0.0.1',
        'user_agent'         => 'Mozilla/5.0',
        'utm_source'         => 'google',
        'utm_medium'         => 'cpc',
        'utm_campaign'       => 'hr-q1',
        'utm_content'        => 'banner',
        'utm_term'           => 'hr software',
        'status'             => 'new',
    ]);

    expect($demo->exists)->toBeTrue();
    expect($demo->id)->not->toBeNull();
    expect($demo->email)->toBe('tom@example.com');
    expect($demo->modules_interested)->toBe(['hr', 'projects']);
    expect($demo->full_name)->toBe('Tom Baker');
});

it('modules_interested is cast to array', function () {
    $demo = DemoRequest::create([
        'first_name'         => 'Jane',
        'last_name'          => 'Doe',
        'email'              => 'jane@example.com',
        'company_name'       => 'Doe Corp',
        'company_size'       => '1-10',
        'modules_interested' => ['hr', 'billing'],
        'status'             => 'new',
    ]);

    $fresh = DemoRequest::find($demo->id);
    expect($fresh->modules_interested)->toBeArray();
    expect($fresh->modules_interested)->toContain('hr');
});

it('uses ULID as primary key', function () {
    $demo = DemoRequest::create([
        'first_name'   => 'Test',
        'last_name'    => 'User',
        'email'        => 'test@ulid.com',
        'company_name' => 'ULID Corp',
        'company_size' => '1-10',
        'status'       => 'new',
    ]);

    // ULIDs are 26 characters long
    expect(strlen($demo->id))->toBe(26);
});

it('can be soft deleted', function () {
    $demo = DemoRequest::create([
        'first_name'   => 'Delete',
        'last_name'    => 'Me',
        'email'        => 'delete@example.com',
        'company_name' => 'Gone Ltd',
        'company_size' => '1-10',
        'status'       => 'new',
    ]);

    $demo->delete();

    expect(DemoRequest::find($demo->id))->toBeNull();
    expect(DemoRequest::withTrashed()->find($demo->id))->not->toBeNull();
});
