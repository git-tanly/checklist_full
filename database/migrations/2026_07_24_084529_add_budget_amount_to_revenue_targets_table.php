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
        Schema::table('revenue_targets', function (Blueprint $table) {
            $table->decimal('budget_amount', 15, 2)->default(0)->after('month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('revenue_targets', function (Blueprint $table) {
            $table->dropColumn('budget_amount');
        });
    }
};
