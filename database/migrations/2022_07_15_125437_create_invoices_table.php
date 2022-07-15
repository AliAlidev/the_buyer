<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->integer("merchant_id")->default(0);
            $table->integer("user_id")->default(0);
            $table->float("total_amount")->default(0);
            $table->float("discount")->default(0);
            $table->float("paid_amount")->default(0);
            $table->integer("invoice_type")->default(0);
            $table->string("notes")->nullable(true);
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
        Schema::dropIfExists('invoices');
    }
}
