<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable(false);
            $table->string('name')->nullable(true);
            $table->float('quantity')->default(0);
            $table->float('price')->default(0);
            $table->string('expiry_date')->nullable(true);
            $table->string('description')->nullable(true);
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
        Schema::dropIfExists('data');
    }
}
