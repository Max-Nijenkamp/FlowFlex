<?php

declare(strict_types=1);

namespace App\Actions;

use App\Contracts\BillingServiceInterface;
use App\Data\ProvisionCompanyData;
use App\Mail\InvitationMail;
use App\Models\Company;
use App\Models\UserInvitation;
use App\Support\Services\CompanyContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Staff-side company onboarding (core.staff-console): company + owner role
 * with every tenant permission + free core modules + owner invitation.
 * Runs from /admin where no CompanyContext exists — sets one for the
 * scoped writes and always forgets it afterwards.
 */
class ProvisionCompanyAction
{
    use AsAction;

    public function handle(ProvisionCompanyData $data): Company
    {
        $context = app(CompanyContext::class);

        try {
            return DB::transaction(function () use ($data, $context): Company {
                $company = Company::create([
                    'name' => $data->name,
                    'slug' => $this->uniqueSlug($data->name),
                    'subscription_status' => 'trialing',
                    'trial_ends_at' => now()->addDays(14),
                    'timezone' => $data->timezone,
                    'locale' => $data->locale,
                    'currency' => $data->currency,
                ]);

                $context->set($company);
                setPermissionsTeamId($company->id);

                // Owner role mirrors LocalDevSeeder: every tenant permission.
                $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web', 'team_id' => $company->id]);
                $owner->syncPermissions(Permission::where('guard_name', 'web')->get());

                app(BillingServiceInterface::class)->seedFreeCoreModules($company->id);

                $invitation = UserInvitation::create([
                    'email' => $data->owner_email,
                    'token' => (string) Str::uuid(),
                    'role' => 'owner',
                    'invited_by' => null, // staff-sent — no tenant sender
                    'expires_at' => now()->addDays(7),
                ]);

                Mail::to($invitation->email)->send(new InvitationMail(
                    company_id: $company->id,
                    companyName: $company->name,
                    inviteUrl: url("/register/invite/{$invitation->token}"),
                    roleName: 'owner',
                ));

                return $company;
            });
        } finally {
            // Never leak tenant context into subsequent admin-panel queries.
            $context->forget();
            setPermissionsTeamId(null);
        }
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;

        for ($i = 2; Company::withTrashed()->where('slug', $slug)->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }
}
