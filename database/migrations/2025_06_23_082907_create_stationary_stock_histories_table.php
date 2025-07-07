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
        Schema::create('stationary_stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stationary_id')->constrained()->onDelete('cascade')->nullable();
            $table->integer('amount')->nullable(); // jumlah penambahan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stationary_stock_histories');
    }
};
