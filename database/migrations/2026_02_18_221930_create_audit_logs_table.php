<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('actor', 50)->default('callcenter'); // kasnije ako ima više usera
            $table->string('action', 100); // cancel_order, complete_pickup, print_marked...
            $table->string('woo_order_id', 50)->nullable()->index();
            $table->json('meta')->nullable();
            $table->string('ip', 60)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
