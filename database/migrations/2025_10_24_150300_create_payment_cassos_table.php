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
        Schema::create('payment_cassos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('bank_id')->constrained('banks');
            $table->string('transaction_code');
            $table->string('package_plan');
            $table->integer('coins');
            $table->integer('amount');
            $table->integer('expiry');
            $table->string('status');
            $table->text('note')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('casso_response')->nullable();
            $table->string('casso_transaction_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_cassos');
    }
};
