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
            $table->uuid('id')->primary();
            $table->text('note')->nullable();
            $table->enum('borrowed_status', ['pending', 'borrowed', 'returned', 'overdue', 'returned (late)', 'cancel'])->default('pending');
            $table->enum('returned_condition', ['good', 'damaged', 'lost'])->nullable();
            $table->uuid('book_id');
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->uuid('copy_id');
            $table->foreign('copy_id')->references('id')->on('book_copies')->onDelete('cascade');
            $table->uuid('batch_id');
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
