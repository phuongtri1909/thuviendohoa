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
        Schema::create('content_images', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Unique key to identify content (content1, content2)');
            $table->string('name')->comment('Display name for admin');
            $table->string('image')->nullable()->comment('Image path');
            $table->string('url')->nullable()->comment('URL to redirect when clicked');
            $table->string('button_text')->nullable()->comment('Button text for overlay button');
            $table->string('button_position_x')->nullable()->comment('Button X position (e.g., 31%)');
            $table->string('button_position_y')->nullable()->comment('Button Y position (e.g., 80%)');
            $table->boolean('status')->default(true)->comment('Active status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_images');
    }
};
