<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\BlogPost;
use App\Models\Marketing\ChangelogEntry;
use App\Models\Marketing\OpenRole;
use App\Models\Marketing\TeamMember;
use Inertia\Inertia;
use Inertia\Response;

class MarketingController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('Welcome');
    }

    public function pricing(): Response
    {
        return Inertia::render('Marketing/Pricing');
    }

    public function features(): Response
    {
        return Inertia::render('Marketing/Features');
    }

    public function about(): Response
    {
        return Inertia::render('Marketing/About', [
            'team' => TeamMember::where('is_published', true)
                ->orderBy('display_order')
                ->get(),
        ]);
    }

    public function contact(): Response
    {
        return Inertia::render('Marketing/Contact');
    }

    public function careers(): Response
    {
        return Inertia::render('Marketing/Careers', [
            'roles' => OpenRole::open()
                ->orderBy('department')
                ->orderBy('title')
                ->get(),
        ]);
    }

    public function changelog(): Response
    {
        return Inertia::render('Marketing/Changelog', [
            'entries' => ChangelogEntry::published()
                ->latest('published_at')
                ->get(),
        ]);
    }

    public function blog(): Response
    {
        $posts = BlogPost::published()
            ->with('category')
            ->latest('published_at')
            ->paginate(12);

        return Inertia::render('Marketing/Blog/Index', [
            'posts' => $posts,
        ]);
    }

    public function blogPost(string $slug): Response
    {
        $post = BlogPost::where('slug', $slug)
            ->published()
            ->with('category')
            ->firstOrFail();

        return Inertia::render('Marketing/Blog/Post', [
            'post' => $post,
        ]);
    }

    public function privacy(): Response
    {
        return Inertia::render('Marketing/Legal/Privacy');
    }

    public function terms(): Response
    {
        return Inertia::render('Marketing/Legal/Terms');
    }

    public function cookies(): Response
    {
        return Inertia::render('Marketing/Legal/Cookies');
    }

    public function dpa(): Response
    {
        return Inertia::render('Marketing/Legal/Dpa');
    }

    public function aup(): Response
    {
        return Inertia::render('Marketing/Legal/Aup');
    }

    public function security(): Response
    {
        return Inertia::render('Marketing/Security');
    }
}

