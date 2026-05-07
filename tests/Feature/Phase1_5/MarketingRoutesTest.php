<?php

use App\Models\Marketing\HelpArticle;
use App\Models\Marketing\HelpCategory;
use App\Models\Module;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('help centre page returns 200', function () {
    $this->get('/help')->assertOk();
});

it('help centre renders with categories', function () {
    HelpCategory::create([
        'name'         => 'Getting Started',
        'slug'         => 'getting-started',
        'is_published' => true,
        'display_order' => 1,
    ]);

    $this->get('/help')->assertOk();
});

it('help article page returns 200 for published article', function () {
    $category = HelpCategory::create([
        'name'         => 'Getting Started',
        'slug'         => 'getting-started',
        'is_published' => true,
        'display_order' => 1,
    ]);

    HelpArticle::create([
        'help_category_id' => $category->id,
        'title'            => 'How to log in',
        'slug'             => 'how-to-log-in',
        'body'             => 'Navigate to /workspace/login.',
        'is_published'     => true,
        'display_order'    => 1,
    ]);

    $this->get('/help/how-to-log-in')->assertOk();
});

it('help article returns 404 for unpublished article', function () {
    $category = HelpCategory::create([
        'name'         => 'Getting Started',
        'slug'         => 'getting-started',
        'is_published' => true,
        'display_order' => 1,
    ]);

    HelpArticle::create([
        'help_category_id' => $category->id,
        'title'            => 'Draft Article',
        'slug'             => 'draft-article',
        'body'             => 'Not ready.',
        'is_published'     => false,
        'display_order'    => 1,
    ]);

    $this->get('/help/draft-article')->assertNotFound();
});

it('module detail page returns 200 for available module', function () {
    Module::create([
        'key'          => 'hr-profiles',
        'name'         => 'Employee Profiles',
        'description'  => 'Manage your team.',
        'domain'       => 'hr',
        'panel_id'     => 'hr',
        'sort_order'   => 1,
        'is_core'      => false,
        'is_available' => true,
    ]);

    $this->get('/modules/hr-profiles')->assertOk();
});

it('module detail page returns 404 for unavailable module', function () {
    Module::create([
        'key'          => 'coming-soon',
        'name'         => 'Coming Soon',
        'description'  => 'Not ready.',
        'domain'       => 'analytics',
        'panel_id'     => 'analytics',
        'sort_order'   => 1,
        'is_core'      => false,
        'is_available' => false,
    ]);

    $this->get('/modules/coming-soon')->assertNotFound();
});

it('module detail page returns 404 for unknown key', function () {
    $this->get('/modules/does-not-exist')->assertNotFound();
});
