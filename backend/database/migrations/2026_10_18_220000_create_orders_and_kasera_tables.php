<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 64)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->restrictOnDelete();
            $table->unsignedBigInteger('amount'); // in whole IDR
            $table->enum('status', ['pending', 'paid', 'failed', 'expired', 'canceled'])->default('pending')->index();
            $table->string('idempotency_key', 255)->nullable()->unique();
            $table->string('kasera_transaction_id', 64)->nullable()->index();
            $table->text('checkout_url')->nullable();
            $table->string('payment_method', 64)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('kasera_pay_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_id', 255)->unique();
            $table->string('event_type', 64)->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kasera_pay_events');
        Schema::dropIfExists('orders');
    }
};
