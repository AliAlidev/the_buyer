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
            // 1 value
            // 2 percentage
            $table->enum("discount_type", [0, 1, 2])->default(0);
            // 1 buy
            // 2 sell
            $table->enum("invoice_type", [0, 1, 2])->default(0);
            // 1 cash
            // 2 debt
            // 3 free
            $table->enum("payment_type", [1, 2, 3])->default(1);
            $table->string("notes")->nullable(true);
            // if pass drug_store as null then add it to general store
            $table->string("drug_store_id")->default(0);
            $table->text('order_number');
            $table->text('customer_name')->nullable();
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
