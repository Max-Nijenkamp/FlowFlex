<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\Core\ApiClient;
use App\Models\Core\ApiToken;
use App\Models\User;
use App\Support\Services\CompanyContext;

describe('API Client', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);
    });

    it('creates api client for company', function () {
        $client = ApiClient::create([
            'company_id'  => $this->company->id,
            'created_by'  => $this->user->id,
            'name'        => 'My Integration',
            'client_id'   => \Illuminate\Support\Str::random(40),
            'scopes'      => ['read', 'write'],
        ]);

        expect($client->company_id)->toBe($this->company->id);
        expect($client->is_active)->toBeTrue();
    });

    it('hides client_secret from serialization', function () {
        $client = ApiClient::create([
            'company_id'    => $this->company->id,
            'created_by'    => $this->user->id,
            'name'          => 'Secure Client',
            'client_id'     => \Illuminate\Support\Str::random(40),
            'client_secret' => 'super-secret-value',
            'scopes'        => [],
        ]);

        $json = $client->toArray();
        expect(isset($json['client_secret']))->toBeFalse();
    });

    it('token isExpired returns true for past expiry', function () {
        $client = ApiClient::create([
            'company_id' => $this->company->id,
            'created_by' => $this->user->id,
            'name'       => 'Test',
            'client_id'  => \Illuminate\Support\Str::random(40),
            'scopes'     => [],
        ]);

        $token = ApiToken::create([
            'api_client_id' => $client->id,
            'token'         => \Illuminate\Support\Str::random(64),
            'scopes'        => [],
            'expires_at'    => now()->subHour(),
        ]);

        expect($token->isExpired())->toBeTrue();
    });

    it('token isExpired returns false for future expiry', function () {
        $client = ApiClient::create([
            'company_id' => $this->company->id,
            'created_by' => $this->user->id,
            'name'       => 'Test',
            'client_id'  => \Illuminate\Support\Str::random(40),
            'scopes'     => [],
        ]);

        $token = ApiToken::create([
            'api_client_id' => $client->id,
            'token'         => \Illuminate\Support\Str::random(64),
            'scopes'        => [],
            'expires_at'    => now()->addDay(),
        ]);

        expect($token->isExpired())->toBeFalse();
    });

    it('client belongs to company only', function () {
        $otherCompany = Company::factory()->create(['status' => 'active']);

        ApiClient::create([
            'company_id' => $this->company->id,
            'created_by' => $this->user->id,
            'name'       => 'Client A',
            'client_id'  => \Illuminate\Support\Str::random(40),
            'scopes'     => [],
        ]);

        expect(ApiClient::where('company_id', $this->company->id)->count())->toBe(1);
        expect(ApiClient::where('company_id', $otherCompany->id)->count())->toBe(0);
    });
});
