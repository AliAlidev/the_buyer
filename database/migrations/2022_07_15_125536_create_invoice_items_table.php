<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->integer("invoice_id");
            $table->integer("data_id");
            $table->float("quantity")->default(0);
            $table->float("quantity_parts")->default(0);
            $table->float("price")->default(0);
            $table->float("price_part")->default(0);
            $table->float("total_quantity_price")->default(0);
            $table->float("total_parts_price")->default(0);
            $table->float("total_price")->default(0);
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
        Schema::dropIfExists('invoice_items');
    }
}
