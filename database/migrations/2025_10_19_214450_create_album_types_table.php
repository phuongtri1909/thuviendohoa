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
        Schema::create('album_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained('albums');
            $table->string('type');
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->unique(['album_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('album_types');
    }
};
