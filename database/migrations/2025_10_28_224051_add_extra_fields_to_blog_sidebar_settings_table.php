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
        Schema::table('blog_sidebar_settings', function (Blueprint $table) {
            $table->string('extra_link_title')->nullable()->after('category_id');
            $table->string('extra_link_url')->nullable()->after('extra_link_title');
            $table->json('banner_images')->nullable()->after('extra_link_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_sidebar_settings', function (Blueprint $table) {
            $table->dropColumn(['extra_link_title', 'extra_link_url', 'banner_images']);
        });
    }
};
