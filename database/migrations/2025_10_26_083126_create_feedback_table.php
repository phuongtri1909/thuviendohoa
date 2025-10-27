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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment('Tên người gửi');
            $table->string('email')->nullable()->comment('Email người gửi');
            $table->text('message')->comment('Nội dung góp ý');
            $table->string('ip_address')->comment('Địa chỉ IP');
            $table->string('user_agent')->nullable()->comment('User Agent');
            $table->enum('status', ['pending', 'read', 'replied'])->default('pending')->comment('Trạng thái');
            $table->text('admin_reply')->nullable()->comment('Phản hồi từ admin');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null')->comment('Admin phản hồi');
            $table->timestamp('replied_at')->nullable()->comment('Thời gian phản hồi');
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
