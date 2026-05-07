<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Marketing\ContactSubmission;
use App\Models\Marketing\DemoRequest;
use App\Models\Marketing\NewsletterSubscriber;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MarketingStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Marketing Pipeline';

    protected function getStats(): array
    {
        $now = now();

        // Demo requests
        $totalDemos   = DemoRequest::withoutGlobalScopes()->count();
        $newDemos     = DemoRequest::withoutGlobalScopes()->where('status', 'new')->count();
        $demos7d      = DemoRequest::withoutGlobalScopes()->where('created_at', '>=', $now->copy()->subDays(7))->count();

        $demoSpark = collect(range(6, 0))->map(
            fn (int $d) => DemoRequest::withoutGlobalScopes()
                ->whereDate('created_at', $now->copy()->subDays($d))
                ->count()
        )->toArray();

        // Contact submissions
        $openContacts = ContactSubmission::withoutGlobalScopes()->where('status', 'new')->count();
        $contacts7d   = ContactSubmission::withoutGlobalScopes()->where('created_at', '>=', $now->copy()->subDays(7))->count();

        $contactSpark = collect(range(6, 0))->map(
            fn (int $d) => ContactSubmission::withoutGlobalScopes()
                ->whereDate('created_at', $now->copy()->subDays($d))
                ->count()
        )->toArray();

        // Newsletter
        $totalSubscribers  = NewsletterSubscriber::withoutGlobalScopes()->where('status', 'subscribed')->count();
        $subscribers7d     = NewsletterSubscriber::withoutGlobalScopes()
            ->where('status', 'subscribed')
            ->where('subscribed_at', '>=', $now->copy()->subDays(7))
            ->count();

        $newsletterSpark = collect(range(6, 0))->map(
            fn (int $d) => NewsletterSubscriber::withoutGlobalScopes()
                ->where('status', 'subscribed')
                ->whereDate('subscribed_at', $now->copy()->subDays($d))
                ->count()
        )->toArray();

        return [
            Stat::make('Demo Requests', $totalDemos)
                ->description("{$newDemos} unreviewed · +{$demos7d} this week")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->chart($demoSpark)
                ->color($newDemos > 5 ? 'danger' : ($newDemos > 0 ? 'warning' : 'success'))
                ->chartColor('warning')
                ->url(route('filament.admin.resources.marketing.demo-requests.index')),

            Stat::make('Open Contact Submissions', $openContacts)
                ->description("+{$contacts7d} received this week")
                ->descriptionIcon('heroicon-m-envelope')
                ->chart($contactSpark)
                ->color($openContacts > 10 ? 'danger' : ($openContacts > 0 ? 'warning' : 'success'))
                ->chartColor('info')
                ->url(route('filament.admin.resources.marketing.contact-submissions.index')),

            Stat::make('Newsletter Subscribers', number_format($totalSubscribers))
                ->description("+{$subscribers7d} this week")
                ->descriptionIcon('heroicon-m-at-symbol')
                ->chart($newsletterSpark)
                ->color('primary')
                ->chartColor('primary')
                ->url(route('filament.admin.resources.marketing.newsletter-subscribers.index')),
        ];
    }
}
