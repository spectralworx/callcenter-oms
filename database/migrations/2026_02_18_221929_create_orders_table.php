<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Woo identifiers
            $table->string('woo_order_id', 50)->unique();     // ID iz Woo
            $table->string('order_number', 50)->nullable()->index();

            // Customer search fields
            $table->string('first_name')->index();
            $table->string('last_name')->index();
            $table->string('phone', 50)->nullable()->index();
            $table->string('email')->nullable()->index();

            // Address
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode', 30)->nullable();

            // Amounts
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->string('currency', 10)->default('RSD');

            // Status
            $table->string('status', 40)->index(); // processing, cancelled, completed...

            // Termal code / pickup
            $table->string('termal_code', 50)->nullable()->index();
            $table->boolean('is_printed')->default(false)->index();
            $table->timestamp('printed_at')->nullable();

            // Tracking (GLS)
            $table->json('tracking_numbers')->nullable();
            $table->timestamp('tracking_updated_at')->nullable();

            // Big message from office
            $table->text('office_notice')->nullable();
            $table->timestamp('office_notice_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
