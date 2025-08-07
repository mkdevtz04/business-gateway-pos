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
        Schema::table('orders', function (Blueprint $table) {
            // This line will remove the incorrect 'quantity' column
            $table->dropColumn('quantity');
        });
    }

    /**
     * Reverse the migrations.
     * The down method allows you to undo this migration if needed.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // This would re-add the column if you rollback the migration
            $table->integer('quantity')->nullable();
        });
    }
};