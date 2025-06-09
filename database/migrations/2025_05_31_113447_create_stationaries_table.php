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
        Schema::create('stationaries', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->foreignId('category_id')
                ->constrained('categories')
                ->onDelete('cascade');
            $table->integer('stock');
            $table->string('image')->nullable();
            $table->string('unit');
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->text('note')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stationaries');
    }
};
