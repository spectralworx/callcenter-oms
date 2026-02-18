<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('incoming_events', function (Blueprint $table) {
            $table->id();

            $table->string('source', 50);              // 'woocommerce'
            $table->string('event_type', 100);         // 'order.created', 'order.updated', 'tracking.updated'
            $table->string('external_id', 100)->nullable(); // order_id/order_number/tracking id
            $table->string('dedupe_key', 191)->unique();    // zaštita od duplih webhookova

            $table->json('payload');
            $table->string('signature', 255)->nullable();   // hmac ili header snapshot
            $table->string('status', 30)->default('queued'); // queued|processed|failed
            $table->text('error')->nullable();

            $table->timestamps();

            $table->index(['source', 'event_type']);
            $table->index(['external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incoming_events');
    }
};
