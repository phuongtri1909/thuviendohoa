<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sets', function (Blueprint $table) {
            $table->string('download_method', 20)->default('coins_only')->after('can_use_free_downloads')->comment('Phương thức tải: both (cả 2), coins_only (chỉ xu), free_only (chỉ lượt miễn phí)');
        });

        // Migrate dữ liệu cũ: nếu can_use_free_downloads = true thì set download_method = 'both', ngược lại = 'coins_only'
        DB::statement("UPDATE sets SET download_method = CASE WHEN can_use_free_downloads = 1 THEN 'both' ELSE 'coins_only' END WHERE type = 'premium'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sets', function (Blueprint $table) {
            $table->dropColumn('download_method');
        });
    }
};
