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
        Schema::create('blog_sidebar_settings', function (Blueprint $table) {
            $table->id();
            $table->string('section_title')->default('CẬP NHẬT XU HƯỚNG THIẾT KẾ');
            $table->foreignId('category_id')->nullable()->constrained('category_blogs')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_sidebar_settings');
    }
};
