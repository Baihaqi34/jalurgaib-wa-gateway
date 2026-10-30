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
        if (!Schema::hasTable('user_login_logs')) {
            Schema::create('user_login_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('email', 191)->index();
                $table->string('name', 191)->nullable();
                $table->string('role', 30)->default('user')->index();
                $table->string('event', 30)->index(); // login, logout, login_failed, register
                $table->string('status', 20)->default('success')->index(); // success, failed
                $table->string('login_method', 50)->default('email_password'); // email_password, google_firebase, manual
                $table->string('ip_address', 45)->nullable()->index();
                $table->text('user_agent')->nullable();
                $table->string('device', 50)->nullable(); // Desktop, Mobile, Tablet
                $table->string('platform', 50)->nullable(); // Windows, Android, iOS, MacOS, Linux
                $table->string('browser', 50)->nullable(); // Chrome, Firefox, Safari, Edge, Opera
                $table->string('failure_reason', 255)->nullable();
                $table->string('description', 255)->nullable();
                $table->timestamp('created_at')->useCurrent()->index();
                $table->timestamp('updated_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_login_logs');
    }
};
