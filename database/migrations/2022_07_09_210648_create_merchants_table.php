<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("email")->nullable(true);
            $table->string("phone")->nullable(false);
            $table->string("tel_phone")->nullable(true);
            $table->string("country")->nullable(true);
            $table->string("city")->nullable(true);
            $table->string("address")->nullable(true);
            $table->string("type")->nullable(true);
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
        Schema::dropIfExists('merchants');
    }
}
