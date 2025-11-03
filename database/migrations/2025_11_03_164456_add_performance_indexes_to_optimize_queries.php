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
        // Adicionar índice composto para otimizar queries de estatísticas mensais
        Schema::table('purchases', function (Blueprint $table) {
            // Índice composto para queries que filtram por product_id e data (para estatísticas mensais)
            if (!Schema::hasIndex('purchases', 'purchases_product_date_index')) {
                $table->index(['product_id', 'purchase_date'], 'purchases_product_date_index');
            }
        });

        // Adicionar índice na coluna category da tabela products para otimizar filtros
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasIndex('products', 'products_category_index')) {
                $table->index('category', 'products_category_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasIndex('purchases', 'purchases_product_date_index')) {
                $table->dropIndex('purchases_product_date_index');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasIndex('products', 'products_category_index')) {
                $table->dropIndex('products_category_index');
            }
        });
    }
};
