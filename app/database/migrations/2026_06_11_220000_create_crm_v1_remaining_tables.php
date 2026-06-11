<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- crm.forecasting ---
        Schema::create('crm_quotas', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->foreignUlid('owner_id')->constrained('users');
            $t->string('period');
            $t->bigInteger('quota_cents');
            $t->string('currency', 3)->default('EUR');
            $t->timestamps();
            $t->unique(['company_id', 'owner_id', 'period']);
        });

        Schema::create('crm_forecast_snapshots', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->foreignUlid('owner_id')->constrained('users');
            $t->string('period');
            $t->string('category');
            $t->bigInteger('amount_cents');
            $t->timestamp('captured_at');
            $t->timestamps();
        });

        Schema::table('crm_deals', function (Blueprint $t): void {
            $t->string('forecast_category')->default('pipeline'); // commit / best-case / pipeline / closed
        });

        // --- crm.segments ---
        Schema::create('crm_segments', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('type')->default('dynamic');
            $t->json('conditions')->nullable();
            $t->integer('member_count')->default(0);
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'name']);
        });

        Schema::create('crm_segment_members', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('segment_id')->constrained('crm_segments')->cascadeOnDelete();
            $t->foreignUlid('company_id')->index();
            $t->foreignUlid('contact_id')->constrained('crm_contacts')->cascadeOnDelete();
            $t->timestamps();
            $t->unique(['segment_id', 'contact_id']);
        });

        // --- crm.scheduling ---
        Schema::create('crm_meeting_types', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->foreignUlid('owner_id')->nullable()->constrained('users');
            $t->string('name');
            $t->string('slug');
            $t->integer('duration_minutes');
            $t->string('location_type')->default('video');
            $t->string('video_link')->nullable();
            $t->integer('buffer_minutes')->default(0);
            $t->bigInteger('price_cents')->default(0);
            $t->json('team_user_ids')->nullable();
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'slug']);
        });

        Schema::create('crm_bookings', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->foreignUlid('meeting_type_id')->constrained('crm_meeting_types');
            $t->foreignUlid('contact_id')->constrained('crm_contacts');
            $t->foreignUlid('assigned_rep_id')->constrained('users');
            $t->timestamp('scheduled_at');
            $t->string('status')->default('confirmed');
            $t->string('stripe_payment_intent_id')->nullable();
            $t->timestamp('reminded_at')->nullable();
            $t->timestamps();
        });

        Schema::create('crm_availability', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->foreignUlid('user_id')->unique()->constrained('users');
            $t->json('working_hours');
            $t->text('calendar_connection')->nullable(); // encrypted (v1.x OAuth)
            $t->timestamps();
        });

        // --- crm.deal-rooms ---
        Schema::create('crm_deal_rooms', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->foreignUlid('deal_id')->unique()->constrained('crm_deals');
            $t->uuid('access_token')->unique();
            $t->json('branding')->nullable();
            $t->timestamp('expires_at');
            $t->timestamp('revoked_at')->nullable();
            $t->timestamps();
        });

        Schema::create('crm_deal_room_documents', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('room_id')->constrained('crm_deal_rooms')->cascadeOnDelete();
            $t->foreignUlid('company_id')->index();
            $t->string('name');
            $t->string('path'); // tenant-scoped storage path
            $t->integer('view_count')->default(0);
            $t->timestamp('last_viewed_at')->nullable();
            $t->timestamps();
        });

        Schema::create('crm_deal_room_action_items', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('room_id')->constrained('crm_deal_rooms')->cascadeOnDelete();
            $t->foreignUlid('company_id')->index();
            $t->string('description');
            $t->string('owner_side'); // buyer / seller
            $t->string('status')->default('open');
            $t->date('due_date')->nullable();
            $t->timestamps();
        });

        Schema::create('crm_deal_room_stakeholders', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('room_id')->constrained('crm_deal_rooms')->cascadeOnDelete();
            $t->foreignUlid('company_id')->index();
            $t->string('name');
            $t->string('role');
            $t->foreignUlid('contact_id')->nullable()->constrained('crm_contacts');
            $t->timestamps();
        });

        // --- crm.contracts ---
        Schema::create('crm_contracts', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->foreignUlid('account_id')->constrained('crm_accounts');
            $t->foreignUlid('deal_id')->nullable()->constrained('crm_deals');
            $t->string('title');
            $t->bigInteger('value_cents')->default(0);
            $t->string('currency', 3)->default('EUR');
            $t->string('billing_interval')->default('one-off');
            $t->date('start_date');
            $t->date('end_date');
            $t->date('renewal_date')->nullable();
            $t->boolean('auto_renew')->default(false);
            $t->integer('notice_period_days')->default(30);
            $t->string('status')->default('draft');
            $t->timestamp('signed_at')->nullable();
            $t->string('signed_pdf_path')->nullable();
            $t->json('alerted_levels')->nullable();
            $t->timestamps();
            $t->softDeletes();
            $t->index(['company_id', 'status', 'renewal_date']);
        });

        // --- crm.email ---
        Schema::create('crm_email_connections', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->foreignUlid('user_id')->constrained('users');
            $t->string('provider');
            $t->text('oauth_token'); // encrypted
            $t->string('email_address');
            $t->boolean('sync_enabled')->default(true);
            $t->string('default_visibility')->default('shared');
            $t->timestamp('last_synced_at')->nullable();
            $t->timestamps();
            $t->unique(['user_id', 'provider']);
        });

        Schema::create('crm_emails', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->foreignUlid('connection_id')->constrained('crm_email_connections');
            $t->foreignUlid('contact_id')->nullable()->constrained('crm_contacts');
            $t->foreignUlid('deal_id')->nullable()->constrained('crm_deals');
            $t->string('direction');
            $t->string('subject');
            $t->text('body');
            $t->string('visibility')->default('shared');
            $t->string('message_id');
            $t->string('thread_id')->nullable();
            $t->string('tracking_token')->unique()->nullable();
            $t->timestamp('sent_at');
            $t->timestamp('opened_at')->nullable();
            $t->timestamp('clicked_at')->nullable();
            $t->timestamps();
            $t->unique(['connection_id', 'message_id']);
            $t->index(['company_id', 'contact_id', 'sent_at']);
        });

        // --- crm.sequences ---
        Schema::create('crm_sequences', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->foreignUlid('owner_id')->nullable()->constrained('users');
            $t->string('trigger_type')->default('manual');
            $t->json('trigger_config')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->softDeletes();
        });

        Schema::create('crm_sequence_steps', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('sequence_id')->constrained('crm_sequences')->cascadeOnDelete();
            $t->foreignUlid('company_id')->index();
            $t->integer('order');
            $t->string('type');
            $t->json('config')->nullable();
            $t->integer('wait_days')->default(0);
            $t->timestamps();
            $t->unique(['sequence_id', 'order']);
        });

        Schema::create('crm_sequence_enrolments', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('sequence_id')->constrained('crm_sequences')->cascadeOnDelete();
            $t->foreignUlid('company_id')->index();
            $t->foreignUlid('contact_id')->constrained('crm_contacts');
            $t->foreignUlid('deal_id')->nullable()->constrained('crm_deals');
            $t->integer('current_step')->default(0);
            $t->string('status')->default('active');
            $t->timestamp('next_step_at');
            $t->json('variant_map')->nullable();
            $t->timestamp('enrolled_at');
            $t->timestamps();
            $t->index(['company_id', 'status', 'next_step_at']);
        });

        // --- crm.pricing ---
        Schema::create('crm_products', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('sku');
            $t->text('description')->nullable();
            $t->string('unit')->default('piece');
            $t->bigInteger('standard_price_cents');
            $t->bigInteger('cost_cents')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'sku']);
        });

        Schema::create('crm_price_books', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('currency', 3)->default('EUR');
            $t->boolean('is_default')->default(false);
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'name']);
        });

        Schema::create('crm_price_book_entries', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('price_book_id')->constrained('crm_price_books')->cascadeOnDelete();
            $t->foreignUlid('product_id')->constrained('crm_products');
            $t->foreignUlid('company_id')->index();
            $t->bigInteger('price_cents');
            $t->date('valid_from')->nullable();
            $t->date('valid_until')->nullable();
            $t->timestamps();
        });

        Schema::create('crm_volume_discounts', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('product_id')->constrained('crm_products')->cascadeOnDelete();
            $t->foreignUlid('company_id')->index();
            $t->decimal('min_quantity', 10, 2);
            $t->decimal('discount_percent', 5, 2);
            $t->timestamps();
            $t->unique(['product_id', 'min_quantity']);
        });

        Schema::table('crm_accounts', function (Blueprint $t): void {
            $t->ulid('price_book_id')->nullable();
        });

        // --- crm.referrals ---
        Schema::create('crm_referral_programs', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->json('referrer_reward')->nullable();
            $t->json('referee_reward')->nullable();
            $t->text('terms')->nullable();
            $t->boolean('is_active')->default(true);
            $t->date('starts_at')->nullable();
            $t->date('ends_at')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });

        Schema::create('crm_referrals', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->foreignUlid('program_id')->constrained('crm_referral_programs');
            $t->foreignUlid('referrer_contact_id')->constrained('crm_contacts');
            $t->string('referral_code');
            $t->string('referee_email');
            $t->foreignUlid('referee_contact_id')->nullable()->constrained('crm_contacts');
            $t->string('status')->default('pending');
            $t->timestamp('converted_at')->nullable();
            $t->timestamp('rewarded_at')->nullable();
            $t->timestamps();
            $t->unique(['company_id', 'referral_code']);
            $t->unique(['program_id', 'referee_email']);
        });

        // --- crm.revenue-intelligence ---
        Schema::create('crm_deal_health', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->foreignUlid('deal_id')->unique()->constrained('crm_deals');
            $t->integer('score');
            $t->json('factors');
            $t->timestamp('calculated_at');
            $t->timestamps();
        });

        Schema::create('crm_win_loss', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->foreignUlid('deal_id')->unique()->constrained('crm_deals');
            $t->string('outcome');
            $t->string('reason');
            $t->string('competitor')->nullable();
            $t->text('notes')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['crm_win_loss', 'crm_deal_health', 'crm_referrals', 'crm_referral_programs',
            'crm_volume_discounts', 'crm_price_book_entries', 'crm_price_books', 'crm_products',
            'crm_sequence_enrolments', 'crm_sequence_steps', 'crm_sequences',
            'crm_emails', 'crm_email_connections', 'crm_contracts',
            'crm_deal_room_stakeholders', 'crm_deal_room_action_items', 'crm_deal_room_documents', 'crm_deal_rooms',
            'crm_availability', 'crm_bookings', 'crm_meeting_types',
            'crm_segment_members', 'crm_segments', 'crm_forecast_snapshots', 'crm_quotas'] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::table('crm_deals', fn (Blueprint $t) => $t->dropColumn('forecast_category'));
        Schema::table('crm_accounts', fn (Blueprint $t) => $t->dropColumn('price_book_id'));
    }
};
