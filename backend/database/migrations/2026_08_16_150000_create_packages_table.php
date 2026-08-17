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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price')->default(0); // in IDR
            $table->unsignedInteger('max_devices')->default(2);
            $table->unsignedInteger('daily_message_limit')->default(50);
            $table->json('benefits')->nullable(); // list of benefit strings
            $table->enum('status', ['active', 'coming_soon', 'inactive'])->default('active');
            $table->string('badge')->nullable(); // e.g. "Gratis", "Populer", "Coming Soon"
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
