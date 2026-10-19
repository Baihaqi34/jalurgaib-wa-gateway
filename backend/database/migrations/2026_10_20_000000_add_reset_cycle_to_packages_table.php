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
        if (Schema::hasTable('packages') && !Schema::hasColumn('packages', 'reset_cycle')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->enum('reset_cycle', ['daily', 'monthly'])
                    ->default('daily')
                    ->after('daily_message_limit')
                    ->comment('Siklus reset kuota pesan paket: daily (harian) atau monthly (bulanan)');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('packages') && Schema::hasColumn('packages', 'reset_cycle')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->dropColumn('reset_cycle');
            });
        }
    }
};
