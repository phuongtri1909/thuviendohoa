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
        Schema::table('purchase_sets', function (Blueprint $table) {
            $table->timestamp('downloaded_at')->nullable()->after('coins');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_sets', function (Blueprint $table) {
            $table->dropColumn('downloaded_at');
        });
    }
};
