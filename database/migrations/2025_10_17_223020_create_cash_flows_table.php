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
        Schema::create('cash_flows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'income' ou 'expense'
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->date('transaction_date');
            $table->string('payment_method')->nullable(); // 'cash', 'card', 'pix', 'transfer'
            $table->string('reference')->nullable(); // Referência externa
            $table->boolean('is_recurring')->default(false);
            $table->json('recurring_config')->nullable(); // Configuração de recorrência
            $table->boolean('is_confirmed')->default(true);
            $table->timestamps();
            
            $table->index(['user_id', 'transaction_date']);
            $table->index(['type', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_flows');
    }
};
