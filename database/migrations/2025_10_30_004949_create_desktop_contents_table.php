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
        Schema::create('desktop_contents', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Unique key to identify desktop content');
            $table->string('name')->comment('Display name for admin');
            $table->string('logo')->nullable()->comment('Logo image path');
            $table->string('title')->comment('Main title');
            $table->text('description')->comment('Description text');
            $table->json('features')->comment('JSON array of feature items (icon, title, description)');
            $table->boolean('status')->default(true)->comment('Active status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desktop_contents');
    }
};
