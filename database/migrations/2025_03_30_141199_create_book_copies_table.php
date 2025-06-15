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
        Schema::create('book_copies', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->enum('status', ['available', 'in_cart', 'borrowed', 'unavailable'])->default('available');
            $table->enum('condition', ['new', 'damaged', 'lost'])->default('new');
            $table->timestamp('acquired_at')->nullable();
            $table->uuid('book_id');
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**t
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_copies');
    }
};
