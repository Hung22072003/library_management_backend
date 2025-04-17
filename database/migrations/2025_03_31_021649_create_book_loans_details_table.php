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
        Schema::create('book_loans_details', function (Blueprint $table) {
            $table->increments('id');
            $table->text('note')->nullable();
            $table->date('returned_at')->nullable();
            $table->enum('borrowed_status', ['pending', 'borrowed', 'returned', 'overdue', 'returned (late)', 'cancel'])->default('pending');
            $table->enum('returned_condition', ['good', 'damaged', 'lost'])->nullable();
            $table->decimal('rental_fee', 8, 0)->nullable();
            $table->decimal('late_fee_per_day', 8, 0)->nullable()->default(5000);
            $table->integer('book_id')->unsigned();
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->integer('batch_id')->unsigned();
            $table->foreign('batch_id')->references('id')->on('book_loans_batches')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_loans_details');
    }
};
