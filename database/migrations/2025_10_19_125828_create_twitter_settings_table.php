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
        Schema::create('twitter_settings', function (Blueprint $table) {
            $table->id();
            $table->string('twitter_client_id');
            $table->text('twitter_client_secret');
            $table->string('twitter_redirect')->default('auth.twitter.callback');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('twitter_settings');
    }
};
