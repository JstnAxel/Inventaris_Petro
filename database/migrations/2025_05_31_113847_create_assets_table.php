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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // custom ID unik
            $table->string('name');
            $table->string('slug');
            $table->foreignId('category_id')
                ->constrained('categories')
                ->onDelete('cascade');
            $table->string('image')->nullable();
            $table->enum('status', ['available', 'loaned', 'maintenance'])->default('available');
            $table->foreignId('user_id') // pencatat/creator
                ->constrained()
                ->onDelete('cascade');
            $table->text('note')->nullable();
            $table->softDeletes(); // Adds nullable deleted_at TIMESTAMP
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
