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
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Nome do objetivo (ex: "Pizza Financeira")
            $table->string('description')->nullable(); // Descrição do objetivo
            $table->decimal('total_income', 12, 2)->default(0); // Total de entradas definido como meta
            $table->json('distribution'); // Distribuição em %: {"fixed_expenses": 40, "investments": 10, ...}
            $table->boolean('is_active')->default(true); // Se o objetivo está ativo
            $table->date('start_date'); // Data de início
            $table->date('end_date')->nullable(); // Data de término (opcional para objetivos contínuos)
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
            $table->index('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
