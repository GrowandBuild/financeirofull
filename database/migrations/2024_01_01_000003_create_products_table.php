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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('unit')->default('un'); // un, kg, L, etc
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('average_price', 10, 2)->default(0);
            $table->decimal('last_price', 10, 2)->default(0);
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->integer('purchase_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
