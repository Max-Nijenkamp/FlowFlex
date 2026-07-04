<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\ProvisionCompanyData;
use App\Mail\InvitationMail;
use App\Models\Company;
use App\Models\UserInvitation;
use App\Services\BillingService;
use App\Support\Services\BuiltInRoles;
use App\Support\Services\CompanyContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * The one create-flow standing up a new customer company (core.staff-console/
 * company-provisioning): company + unique slug -> built-in roles -> free core
 * modules -> owner invitation. Context is set and forgotten in finally so it
 * never leaks into later admin queries.
 */
class ProvisionCompanyAction
{
    use AsAction;

    public function handle(ProvisionCompanyData $data): Company
    {
        try {
            return DB::transaction(function () use ($data): Company {
                $company = Company::query()->create([
                    'name' => $data->name,
                    'slug' => $this->uniqueSlug($data->name),
                    'subscription_status' => 'trial',
                    'timezone' => $data->timezone,
                    'locale' => $data->locale,
                    'currency' => $data->currency,
                    'trial_ends_at' => now()->addDays(30),
                ]);

                app(CompanyContext::class)->set($company);

                BuiltInRoles::ensure($company);
                app(BillingService::class)->seedFreeCoreModules($company);

                $invitation = UserInvitation::query()->create([
                    'company_id' => $company->id,
                    'email' => $data->owner_email,
                    'role' => 'owner', // provisioning is the single sanctioned owner invite
                    'token' => (string) Str::uuid(),
                    'invited_by' => null,
                    'expires_at' => now()->addDays(7),
                ]);

                Mail::to($invitation->email)->queue(InvitationMail::forInvitation($invitation));

                return $company;
            });
        } finally {
            app(CompanyContext::class)->forget();
        }
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) !== '' ? Str::slug($name) : 'company';
        $slug = $base;
        $suffix = 1;

        while (Company::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$suffix);
        }

        return $slug;
    }
}
