<?php

declare(strict_types=1);

use App\Events\Foundation\UserInvited;
use App\Listeners\Foundation\SendInviteMailListener;
use App\Mail\Foundation\UserInvitedMail;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

describe('Invite Mail', function () {
    it('sends invite mail when UserInvited event fires', function () {
        Mail::fake();

        $company = Company::factory()->create(['status' => 'active']);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'status'     => 'invited',
            'email'      => 'new@example.com',
        ]);

        $token = \Illuminate\Support\Str::random(64);
        $listener = new SendInviteMailListener();
        $listener->handle(new UserInvited($user, $company, $token));

        Mail::assertSent(UserInvitedMail::class, function ($mail) use ($user, $token) {
            return $mail->hasTo($user->email)
                && $mail->inviteToken === $token;
        });
    });

    it('mail contains correct accept URL', function () {
        $company = Company::factory()->create(['status' => 'active']);
        $user = User::factory()->create(['company_id' => $company->id, 'email' => 'test@example.com']);
        $token = 'test-token-12345';

        $mail = new UserInvitedMail($user, $company, $token);
        $content = $mail->content();

        expect($content->view)->toBe('emails.foundation.user-invited');
        expect($content->with['acceptUrl'])->toContain("/invite/{$token}");
    });

    it('event listener is registered', function () {
        $listeners = app(\Illuminate\Contracts\Events\Dispatcher::class)
            ->getListeners(\App\Events\Foundation\UserInvited::class);

        expect(count($listeners))->toBeGreaterThanOrEqual(1);
    });
});
