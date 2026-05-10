<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\User;

describe('Locale Middleware', function () {
    it('sets locale from user preference', function () {
        $company = Company::factory()->create(['status' => 'active']);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'status'     => 'active',
            'locale'     => 'nl',
        ]);

        $this->actingAs($user, 'web');
        $this->get('/');

        expect(app()->getLocale())->toBe('nl');
    });

    it('falls back to app default for unknown locale', function () {
        $company = Company::factory()->create(['status' => 'active']);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'status'     => 'active',
            'locale'     => 'xx', // unsupported
        ]);

        $this->actingAs($user, 'web');
        $this->get('/');

        expect(app()->getLocale())->toBe(config('app.locale', 'en'));
    });

    it('unauthenticated request does not throw and uses Accept-Language header', function () {
        $response = $this->withHeaders(['Accept-Language' => 'de'])->get('/');

        // Should not throw; middleware handles unauthenticated gracefully
        expect(in_array($response->status(), [200, 302]))->toBeTrue();
        expect(app()->getLocale())->toBe('de');
    });

    it('falls back to app default when Accept-Language is unsupported', function () {
        $response = $this->withHeaders(['Accept-Language' => 'zh-CN'])->get('/');

        expect(in_array($response->status(), [200, 302]))->toBeTrue();
        expect(app()->getLocale())->toBe(config('app.locale', 'en'));
    });

    it('Accept-Language with region code uses only the language part', function () {
        $response = $this->withHeaders(['Accept-Language' => 'fr-FR,fr;q=0.9'])->get('/');

        expect(in_array($response->status(), [200, 302]))->toBeTrue();
        expect(app()->getLocale())->toBe('fr');
    });

    it('all supported locales are accepted from user preference', function () {
        $supported = ['en', 'nl', 'de', 'fr', 'es'];

        foreach ($supported as $locale) {
            $company = Company::factory()->create(['status' => 'active']);
            $user = User::factory()->create([
                'company_id' => $company->id,
                'locale'     => $locale,
            ]);

            $this->actingAs($user, 'web');
            $this->get('/');

            expect(app()->getLocale())->toBe($locale, "Locale {$locale} should be accepted");

            auth()->guard('web')->logout();
        }
    });
});
