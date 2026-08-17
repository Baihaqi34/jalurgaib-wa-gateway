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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'status')) {
                    $table->string('status')->default('active')->after('role'); // active, suspended, banned
                }
                if (!Schema::hasColumn('users', 'suspend_reason')) {
                    $table->text('suspend_reason')->nullable()->after('status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('users', 'suspend_reason')) {
                    $table->dropColumn('suspend_reason');
                }
            });
        }
    }
};
