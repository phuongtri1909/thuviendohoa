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
        Schema::create('coin_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('amount')->comment('Số xu cộng/trừ (có thể âm)');
            $table->string('type')->comment('payment, purchase, manual, monthly_bonus');
            $table->string('source')->comment('Nguồn gốc: payment_id, purchase_id, transaction_id, bonus_id');
            $table->string('reason')->comment('Lý do cộng/trừ xu');
            $table->text('description')->nullable()->comment('Mô tả chi tiết');
            $table->json('metadata')->nullable()->comment('Dữ liệu bổ sung');
            $table->boolean('is_read')->default(false)->comment('Đã đọc hay chưa');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['type', 'created_at']);
            $table->index(['is_read', 'created_at']);
            $table->index(['source', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coin_histories');
    }
};