<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAmountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('amounts', function (Blueprint $table) {
            // 0 default
            // 1 start and expiry date
            // 2 start and number of available months
            // 3 olny expiry date
            $table->enum('expiry_type', [0, 1, 2, 3])->default(0);
            // invoice id where the amount come from
            $table->bigInteger('amount_type_id')->default(0);
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
            $table->dropColumn('expiry_type');
            $table->dropColumn('amount_type_id');
        });
    }
}
