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
        Schema::create('monthly_bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->string('month')->comment('Tháng thực hiện (Y-m)');
            $table->integer('total_users')->comment('Tổng số user được cộng');
            $table->integer('total_coins')->comment('Tổng số xu đã cộng');
            $table->integer('bonus_per_user')->comment('Số xu cộng cho mỗi user');
            $table->json('user_ids')->comment('Danh sách user_id được cộng');
            $table->text('notes')->nullable()->comment('Ghi chú thêm');
            $table->timestamp('processed_at')->comment('Thời gian xử lý');
            $table->timestamps();
            
            $table->index(['package_id', 'month']);
            $table->index(['month', 'processed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_bonuses');
    }
};
