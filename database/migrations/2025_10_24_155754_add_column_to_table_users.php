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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->constrained('packages');
            $table->timestamp('package_expired_at')->default(now());
            $table->integer('coins')->default(0);
            $table->integer('free_downloads')->default(2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn('package_id');
            $table->dropColumn('package_expired_at');
            $table->dropColumn('coins');
            $table->dropColumn('free_downloads');
        });
    }
};
