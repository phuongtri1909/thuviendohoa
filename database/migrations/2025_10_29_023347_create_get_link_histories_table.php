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
        Schema::create('get_link_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('url', 2048)->comment('URL gốc người dùng nhập');
            $table->string('title')->nullable()->comment('Title của trang web');
            $table->string('favicon')->nullable()->comment('URL favicon của trang web');
            $table->integer('coins_spent')->comment('Số xu đã trừ');
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('get_link_histories');
    }
};
