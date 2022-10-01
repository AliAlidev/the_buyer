<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountTypeForAmountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('amounts', function (Blueprint $table) {
            // 0 inventory amount
            // 1 buy amount 
            // 2 sell amount
            $table->enum('amount_type', [0, 1, 2]);
            $table->float('real_price')->default(0);
            $table->float('real_part_price')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('amounts', function (Blueprint $table) {
            $table->dropColumn('amount_type');
            $table->dropColumn('real_price');
            $table->dropColumn('real_part_price');
        });
    }
}
