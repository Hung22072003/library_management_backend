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
        Schema::create('book_copy_conditions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('copy_id');
            $table->uuid('user_id');
            $table->uuid('batch_id');
            $table->text('condition_note')->nullable();
            $table->foreign('copy_id')->references('id')->on('book_copies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('batch_id')->references('id')->on('book_loans_batches')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_copy_conditions');
    }
};
