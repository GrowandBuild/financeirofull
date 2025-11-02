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
        Schema::create('financial_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'income' ou 'expense'
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->date('scheduled_date'); // Data agendada
            $table->string('image_path')->nullable(); // Imagem representativa
            $table->boolean('is_confirmed')->default(false); // Se foi confirmado
            $table->timestamp('confirmed_at')->nullable(); // Quando foi confirmado
            $table->timestamps();
            
            $table->index(['user_id', 'scheduled_date']);
            $table->index(['type', 'scheduled_date']);
            $table->index('is_confirmed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_schedules');
    }
};
