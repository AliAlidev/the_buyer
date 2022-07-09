<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuyInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buy_invoices', function (Blueprint $table) {
            $table->id();
            $table->integer("data_id");
            $table->float("quantity")->default(0);
            $table->float("quantity_parts")->default(0);
            $table->float("price")->default(0);
            $table->float("price_part")->default(0);
            $table->float("total")->default(0);
            $table->float("total_parts")->default(0);
            $table->float("total_final")->default(0);
            $table->integer("merchant_id")->default(0);
            $table->integer("user_id")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('buy_invoices');
    }
}
