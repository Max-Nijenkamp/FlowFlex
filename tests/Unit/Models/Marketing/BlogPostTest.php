<?php

use App\Models\Marketing\BlogCategory;
use App\Models\Marketing\BlogPost;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function makeCategory(): BlogCategory
{
    static $counter = 0;
    $counter++;

    return BlogCategory::create([
        'name'          => 'Test Category ' . $counter,
        'slug'          => 'test-category-' . $counter,
        'is_published'  => true,
        'display_order' => $counter,
    ]);
}

// ---------- scopePublished ----------

it('scopePublished returns posts with status published and published_at in the past', function () {
    $cat = makeCategory();

    BlogPost::create([
        'blog_category_id' => $cat->id,
        'title'            => 'Published Post',
        'slug'             => 'published-post',
        'body'             => str_repeat('FlowFlex automates your HR processes. ', 30),
        'status'           => 'published',
        'published_at'     => now()->subDay(),
    ]);

    expect(BlogPost::published()->count())->toBe(1);
});

it('scopePublished excludes posts with status draft', function () {
    $cat = makeCategory();

    BlogPost::create([
        'blog_category_id' => $cat->id,
        'title'            => 'Draft Post',
        'slug'             => 'draft-post',
        'body'             => str_repeat('Draft content that is not yet live. ', 20),
        'status'           => 'draft',
        'published_at'     => now()->subDay(),
    ]);

    expect(BlogPost::published()->count())->toBe(0);
});

it('scopePublished excludes posts with future published_at', function () {
    $cat = makeCategory();

    BlogPost::create([
        'blog_category_id' => $cat->id,
        'title'            => 'Scheduled Post',
        'slug'             => 'scheduled-post',
        'body'             => str_repeat('Coming soon content about the product. ', 20),
        'status'           => 'published',
        'published_at'     => now()->addDay(),
    ]);

    expect(BlogPost::published()->count())->toBe(0);
});

it('scopePublished excludes posts with null published_at', function () {
    $cat = makeCategory();

    BlogPost::create([
        'blog_category_id' => $cat->id,
        'title'            => 'Unpublished Post',
        'slug'             => 'unpublished-post',
        'body'             => str_repeat('Unpublished content sitting in draft. ', 20),
        'status'           => 'published',
        'published_at'     => null,
    ]);

    expect(BlogPost::published()->count())->toBe(0);
});

it('scopePublished returns multiple matching posts', function () {
    $cat = makeCategory();

    BlogPost::create([
        'blog_category_id' => $cat->id,
        'title'            => 'First Published',
        'slug'             => 'first-published',
        'body'             => str_repeat('FlowFlex is great for SMEs. ', 30),
        'status'           => 'published',
        'published_at'     => now()->subDays(2),
    ]);

    BlogPost::create([
        'blog_category_id' => $cat->id,
        'title'            => 'Second Published',
        'slug'             => 'second-published',
        'body'             => str_repeat('Learn how to use FlowFlex modules. ', 30),
        'status'           => 'published',
        'published_at'     => now()->subDays(1),
    ]);

    BlogPost::create([
        'blog_category_id' => $cat->id,
        'title'            => 'Draft Post',
        'slug'             => 'draft-post',
        'body'             => str_repeat('Draft content still in progress. ', 20),
        'status'           => 'draft',
        'published_at'     => null,
    ]);

    expect(BlogPost::published()->count())->toBe(2);
});

// ---------- reading_time auto-calculation ----------

it('reading_time is auto-calculated as greater than 0 for a post with body content', function () {
    $cat  = makeCategory();
    $post = BlogPost::create([
        'blog_category_id' => $cat->id,
        'title'            => 'Article With Content',
        'slug'             => 'article-with-content',
        'body'             => str_repeat('FlowFlex helps HR teams streamline their workflows and save time. ', 50),
        'status'           => 'draft',
        'published_at'     => null,
    ]);

    expect($post->reading_time)->toBeGreaterThan(0);
});

it('reading_time is at least 1 for a very short body', function () {
    $cat  = makeCategory();
    $post = BlogPost::create([
        'blog_category_id' => $cat->id,
        'title'            => 'Short Post',
        'slug'             => 'short-post',
        'body'             => 'Hello world.',
        'status'           => 'draft',
    ]);

    expect($post->reading_time)->toBe(1);
});

it('reading_time is calculated at approximately 200 words per minute', function () {
    // 400 words at 200 wpm = 2 minutes
    $body = implode(' ', array_fill(0, 400, 'word'));
    $cat  = makeCategory();

    $post = BlogPost::create([
        'blog_category_id' => $cat->id,
        'title'            => '400 Word Post',
        'slug'             => '400-word-post',
        'body'             => $body,
        'status'           => 'draft',
    ]);

    expect($post->reading_time)->toBe(2);
});

it('reading_time is recalculated when body is updated', function () {
    $cat  = makeCategory();
    $post = BlogPost::create([
        'blog_category_id' => $cat->id,
        'title'            => 'Updatable Post',
        'slug'             => 'updatable-post',
        'body'             => 'Short body.',
        'status'           => 'draft',
    ]);

    $initialTime = $post->reading_time;

    $post->update([
        'body' => str_repeat('Much longer content that takes more time to read. ', 100),
    ]);

    expect($post->reading_time)->toBeGreaterThan($initialTime);
});

// ---------- category relationship ----------

it('belongs to a BlogCategory', function () {
    $category = BlogCategory::create([
        'name'          => 'Product Updates',
        'slug'          => 'product-updates',
        'is_published'  => true,
        'display_order' => 1,
    ]);

    $post = BlogPost::create([
        'blog_category_id' => $category->id,
        'title'            => 'New Feature',
        'slug'             => 'new-feature',
        'body'             => str_repeat('Exciting new feature released today. ', 20),
        'status'           => 'published',
        'published_at'     => now()->subHour(),
    ]);

    expect($post->category->id)->toBe($category->id);
    expect($post->category)->toBeInstanceOf(BlogCategory::class);
});

// ---------- soft deletes ----------

it('can be soft deleted', function () {
    $cat  = makeCategory();
    $post = BlogPost::create([
        'blog_category_id' => $cat->id,
        'title'            => 'Delete Me',
        'slug'             => 'delete-me',
        'body'             => 'Some content.',
        'status'           => 'draft',
    ]);

    $post->delete();

    expect(BlogPost::find($post->id))->toBeNull();
    expect(BlogPost::withTrashed()->find($post->id))->not->toBeNull();
});
