<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_id');
            $table->integer("amount")->default(0);
            $table->float("amount_part")->default(0);
            $table->float("price")->default(0);
            $table->float("price_part")->default(0);
            $table->string("start_date")->nullable(true);
            $table->string("expiry_date")->nullable(true);
            $table->integer("merchant_id")->default(0);
            $table->integer("user_id")->default(0);
            // 1 buy return, 2 sell return
            $table->enum('return_type', ['0', '1', '2']);
            // contain id of customer or drugStore
            $table->foreignId('return_side_id');
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
        Schema::dropIfExists('product_returns');
    }
}
