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
        // Apenas índices essenciais que não conflitam
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasIndex('purchases', 'purchases_product_id_index')) {
                $table->index('product_id', 'purchases_product_id_index');
            }
            
            if (!Schema::hasIndex('purchases', 'purchases_date_index')) {
                $table->index('purchase_date', 'purchases_date_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex('purchases_product_id_index');
            $table->dropIndex('purchases_date_index');
        });
    }
};
