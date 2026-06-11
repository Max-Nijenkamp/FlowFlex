<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary(); // framework convention
            $table->string('type');
            $table->ulidMorphs('notifiable'); // ULID users
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->ulid('company_id')->nullable()->index(); // tenant scope
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
