<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->string('to');                        // recipient phone number
            $table->text('message');
            $table->string('type')->default('text');    // text, image, video, document
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->string('wa_message_id')->nullable(); // WhatsApp message ID from Go service
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('min_delay')->default(1000); // ms, anti-ban
            $table->integer('max_delay')->default(4000); // ms, anti-ban
            $table->timestamps();

            $table->index(['device_id', 'status']);
            $table->index('to');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
