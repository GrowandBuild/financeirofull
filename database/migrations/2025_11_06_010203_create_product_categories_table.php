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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome da categoria (ex: "Vícios")
            $table->string('normalized_name')->unique(); // Nome normalizado para busca (ex: "vicios")
            $table->string('slug')->unique(); // Slug para URLs
            $table->text('aliases')->nullable(); // JSON com variações e sinônimos
            $table->integer('usage_count')->default(0); // Quantidade de produtos usando
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Índices para performance
            $table->index('normalized_name');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
