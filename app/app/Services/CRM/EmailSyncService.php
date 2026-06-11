<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Models\CRM\Activity;
use App\Models\CRM\Contact;
use App\Models\CRM\CrmEmail;
use App\Models\CRM\EmailConnection;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class EmailSyncService
{
    /**
     * Incremental pull from the provider API since last_synced_at.
     * Per-message try/catch; dedupes on (connection, message_id); matches
     * contacts by address; pauses sequences on inbound replies.
     *
     * @return array{synced: int, skipped: int, failed: int}
     */
    public function sync(string $connectionId): array
    {
        $connection = EmailConnection::query()->findOrFail($connectionId);
        $result = ['synced' => 0, 'skipped' => 0, 'failed' => 0];

        $messages = $this->fetchMessages($connection);

        foreach ($messages as $message) {
            try {
                $exists = CrmEmail::query()
                    ->where('connection_id', $connection->id)
                    ->where('message_id', $message['message_id'])
                    ->exists();

                if ($exists) {
                    $result['skipped']++;

                    continue;
                }

                $counterparty = $message['direction'] === 'inbound' ? $message['from'] : $message['to'];
                $contact = Contact::query()->where('email', $counterparty)->first();

                CrmEmail::create([
                    'connection_id' => $connection->id,
                    'contact_id' => $contact?->id,
                    'direction' => $message['direction'],
                    'subject' => $message['subject'],
                    'body' => $this->purify($message['body']),
                    'visibility' => $connection->default_visibility,
                    'message_id' => $message['message_id'],
                    'thread_id' => $message['thread_id'] ?? null,
                    'sent_at' => $message['sent_at'],
                ]);

                if ($contact !== null) {
                    Activity::create([
                        'company_id' => $connection->company_id,
                        'type' => 'email',
                        'subject' => $message['subject'],
                        'contact_id' => $contact->id,
                        'owner_id' => $connection->user_id,
                        'completed_at' => $message['sent_at'],
                    ]);

                    if ($message['direction'] === 'inbound') {
                        app(SequenceService::class)->pauseOnReply($contact->id);
                    }
                }

                $result['synced']++;
            } catch (Throwable $e) {
                report($e);
                $result['failed']++;
            }
        }

        $connection->update(['last_synced_at' => now()]);

        return $result;
    }

    public function disconnect(string $connectionId): void
    {
        $connection = EmailConnection::query()->findOrFail($connectionId);

        // Best-effort provider revocation — synced mail is kept.
        try {
            Http::timeout(5)->post($this->revokeUrl($connection->provider), [
                'token' => $connection->oauth_token,
            ]);
        } catch (Throwable) {
            // Token may already be dead — disconnect locally regardless.
        }

        $connection->update(['oauth_token' => Str::random(8), 'sync_enabled' => false]);
    }

    /** @return array<array{message_id: string, direction: string, from: string, to: string, subject: string, body: string, thread_id?: string, sent_at: string}> */
    private function fetchMessages(EmailConnection $connection): array
    {
        $since = $connection->last_synced_at?->toIso8601String() ?? now()->subDays(30)->toIso8601String();

        $response = Http::withToken($connection->oauth_token)
            ->get($this->messagesUrl($connection->provider), ['since' => $since]);

        return $response->successful() ? ($response->json('messages') ?? []) : [];
    }

    private function messagesUrl(string $provider): string
    {
        return match ($provider) {
            'gmail' => 'https://gmail.googleapis.com/gmail/v1/users/me/messages',
            default => 'https://graph.microsoft.com/v1.0/me/messages',
        };
    }

    private function revokeUrl(string $provider): string
    {
        return match ($provider) {
            'gmail' => 'https://oauth2.googleapis.com/revoke',
            default => 'https://login.microsoftonline.com/common/oauth2/v2.0/logout',
        };
    }

    /** Rich text XSS prevention (security.md — ezyang/htmlpurifier). */
    private function purify(string $html): string
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null);

        return (new HTMLPurifier($config))->purify($html);
    }
}
