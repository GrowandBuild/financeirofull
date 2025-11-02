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
        Schema::table('financial_schedules', function (Blueprint $table) {
            $table->boolean('is_recurring')->default(false)->after('is_confirmed');
            $table->json('recurring_config')->nullable()->after('is_recurring');
            $table->boolean('is_cancelled')->default(false)->after('recurring_config');
            $table->timestamp('cancelled_at')->nullable()->after('is_cancelled');
            $table->text('cancellation_reason')->nullable()->after('cancelled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_schedules', function (Blueprint $table) {
            $table->dropColumn([
                'is_recurring',
                'recurring_config',
                'is_cancelled',
                'cancelled_at',
                'cancellation_reason'
            ]);
        });
    }
};
