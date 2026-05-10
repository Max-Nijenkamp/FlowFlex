<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add ends_at to billing_subscriptions (used when subscription is deleted)
        if (! Schema::hasColumn('billing_subscriptions', 'ends_at')) {
            Schema::table('billing_subscriptions', function (Blueprint $table): void {
                $table->timestamp('ends_at')->nullable()->after('canceled_at');
            });
        }

        // Add stripe_customer_id to companies for direct lookup if needed
        if (! Schema::hasColumn('companies', 'stripe_customer_id')) {
            Schema::table('companies', function (Blueprint $table): void {
                $table->string('stripe_customer_id')->nullable()->after('subscribed_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('billing_subscriptions', 'ends_at')) {
            Schema::table('billing_subscriptions', function (Blueprint $table): void {
                $table->dropColumn('ends_at');
            });
        }

        if (Schema::hasColumn('companies', 'stripe_customer_id')) {
            Schema::table('companies', function (Blueprint $table): void {
                $table->dropColumn('stripe_customer_id');
            });
        }
    }
};
