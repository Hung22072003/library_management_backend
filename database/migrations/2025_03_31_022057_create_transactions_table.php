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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('note')->nullable();
            $table->decimal('amount', 8, 0);
            $table->enum('type', ['late_fee', 'lost_fee', 'damaged_fee', 'other'])->nullable();   
            $table->enum('payment_status', ['pending', 'failed', 'success'])->default('pending');
            $table->enum('payment_method', ['cash', 'momo', 'vnpay'])->nullable();
            $table->timestamp('payment_expired_at')->nullable();
            $table->string('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->uuid('copy_id')->nullable();
            $table->foreign('copy_id')->references('id')->on('book_copies')->onDelete('cascade');
            $table->uuid('batch_id')->nullable();
            $table->foreign('batch_id')->references('id')->on('book_loans_batches')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
