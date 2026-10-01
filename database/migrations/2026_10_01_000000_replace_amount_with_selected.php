<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Items that had a quantity were on the purchase list, keep them there.
        DB::table('groceries')->where('amount', '>', 0)->update(['selected' => true]);

        Schema::table('groceries', function (Blueprint $table) {
            $table->dropColumn('amount');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groceries', function (Blueprint $table) {
            $table->integer('amount')->default(0)->comment('Quantity of the grocery item');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->integer('amount')->default(0)->comment('Quantity bought');
        });

        DB::table('groceries')->where('selected', true)->update(['amount' => 1]);
    }
};
