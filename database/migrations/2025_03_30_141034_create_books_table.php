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
        Schema::create('books', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('publication_year')->nullable();
            $table->string('isbn13')->nullable()->unique();
            $table->string('isbn10')->nullable()->unique();
            $table->integer('available_copies')->nullable();
            $table->integer('total_copies')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('language')->nullable();
            $table->string('authors')->nullable();
            $table->integer('num_pages')->nullable();
            $table->string('floor')->nullable();
            $table->string('shelf')->nullable();
            $table->string('row')->nullable();
            $table->string('col')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
