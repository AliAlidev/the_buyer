<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_dates', function (Blueprint $table) {
            $table->id();
            $table->integer("data_id")->default(0);
            $table->string("start_date")->nullable(true);
            $table->string("expiry_date")->nullable(true);
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
        Schema::dropIfExists('item_dates');
    }
}
