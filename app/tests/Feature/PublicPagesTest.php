<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia;

it('serves the Switchboard+ expansion pages', function (string $url, string $component) {
    $this->get($url)
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page) => $page->component($component));
})->with([
    ['/modules', 'Marketing/Catalogue'],
    ['/switch-over', 'Marketing/SwitchOver'],
    ['/trust', 'Marketing/Trust'],
    ['/changelog', 'Marketing/Changelog'],
    ['/patchwork', 'Marketing/Patchwork'],
    ['/customers/veldkamp', 'Marketing/CaseStudy'],
    ['/status', 'Marketing/Status'],
    ['/help', 'Marketing/Help/Index'],
    ['/help/activate-a-module', 'Marketing/Help/Article'],
]);

it('renders the branded 404 on unknown public urls', function () {
    $this->get('/definitely-not-a-page')
        ->assertNotFound()
        ->assertInertia(fn (AssertableInertia $page) => $page->component('Marketing/NotFound'));
});

it('404s unknown case studies and help articles', function (string $url) {
    $this->get($url)->assertNotFound();
})->with([
    ['/customers/nope'],
    ['/help/nope'],
]);

it('lists the new pages in the sitemap', function () {
    $this->get('/sitemap.xml')
        ->assertSuccessful()
        ->assertSee('/modules')
        ->assertSee('/switch-over')
        ->assertSee('/trust')
        ->assertSee('/status');
});
