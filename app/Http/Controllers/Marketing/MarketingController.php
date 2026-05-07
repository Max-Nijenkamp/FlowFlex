<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\BlogPost;
use App\Models\Marketing\ChangelogEntry;
use App\Models\Marketing\HelpArticle;
use App\Models\Marketing\HelpCategory;
use App\Models\Marketing\OpenRole;
use App\Models\Marketing\TeamMember;
use App\Models\Module;
use Inertia\Inertia;
use Inertia\Response;

class MarketingController extends Controller
{
    public function home(): Response
    {
        $domains = $this->domainsData();

        return Inertia::render('Welcome', [
            'domains' => $domains,
            'moduleCount' => array_sum(array_column($domains, 'count')),
            'domainCount' => count($domains),
        ]);
    }

    public function pricing(): Response
    {
        return Inertia::render('Marketing/Pricing');
    }

    public function features(): Response
    {
        return Inertia::render('Marketing/Features', [
            'domains' => $this->domainsData(),
        ]);
    }

    private function domainsData(): array
    {
        return Module::where('is_available', true)
            ->orderBy('domain')
            ->orderBy('sort_order')
            ->get(['name', 'description', 'domain'])
            ->groupBy('domain')
            ->map(fn ($modules, $domain) => [
                'key' => $domain,
                'count' => $modules->count(),
                'modules' => $modules->map(fn ($m) => [
                    'name' => $m->name,
                    'description' => $m->description,
                ])->values()->toArray(),
            ])
            ->values()
            ->toArray();
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

    public function help(): Response
    {
        $categories = HelpCategory::where('is_published', true)
            ->whereNull('parent_id')
            ->with(['articles' => fn ($q) => $q->where('is_published', true)->orderBy('display_order')])
            ->orderBy('display_order')
            ->get();

        return Inertia::render('Marketing/Help', [
            'categories' => $categories,
        ]);
    }

    public function helpArticle(string $slug): Response
    {
        $article = HelpArticle::where('slug', $slug)
            ->where('is_published', true)
            ->with('category')
            ->firstOrFail();

        return Inertia::render('Marketing/HelpArticle', [
            'article' => $article,
        ]);
    }

    public function moduleDetail(string $key): Response
    {
        $module = Module::where('key', $key)->where('is_available', true)->first();

        if (! $module) {
            abort(404);
        }

        $module->load('subModules');

        return Inertia::render('Marketing/Module', [
            'module' => $module,
        ]);
    }
}

