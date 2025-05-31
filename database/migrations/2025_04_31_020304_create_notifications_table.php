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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->enum('type', ['overdue_loan', 'cancel_loan', 'borrow_loan', 'overdue_payment', 'success_payment'])->nullable();
            $table->string('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->uuid('batch_id')->nullable();
            $table->foreign('batch_id')->references('id')->on('book_loans_batches')->onDelete('cascade');
            $table->uuid('transaction_id')->nullable();
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
