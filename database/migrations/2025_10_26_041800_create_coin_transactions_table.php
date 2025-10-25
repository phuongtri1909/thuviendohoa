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
        Schema::create('coin_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->integer('amount')->comment('Số xu cộng (có thể âm để trừ)');
            $table->string('type')->default('manual')->comment('manual, package_bonus, etc');
            $table->string('reason')->comment('Lý do cộng xu');
            $table->text('note')->nullable()->comment('Ghi chú thêm');
            $table->json('target_data')->nullable()->comment('Dữ liệu đích (package_id, user_ids, etc)');
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['admin_id', 'created_at']);
            $table->index(['type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coin_transactions');
    }
};
