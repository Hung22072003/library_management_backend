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
            $table->increments('id');
            $table->decimal('amount', 8, 0);
            $table->enum('type', ['rental_fee', 'late_fee', 'lost_fee', 'damaged_fee', 'other'])->default('rental_fee');   
            $table->enum('payment_status', ['pending', 'failed', 'success'])->default('pending');
            $table->enum('payment_method', ['cash', 'momo', 'vnpay'])->default('cash');
            $table->timestamp('payment_expired_at');
            $table->string('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('book_id')->unsigned()->nullable();
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->integer('batch_id')->unsigned()->nullable();
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
