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
            $table->string('payment_method', 20)->default('coins')->after('coins')->comment('Phương thức thanh toán: coins hoặc free_download');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_sets', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};
