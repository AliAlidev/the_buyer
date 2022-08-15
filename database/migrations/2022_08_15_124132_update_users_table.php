<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string("phone")->nullable(false);
            $table->string("tel_phone")->nullable(true);
            $table->string("country")->nullable(true);
            $table->string("city")->nullable(true);
            $table->string("address")->nullable(true);
            $table->integer("merchant_type")->default(0); // 1 Pharmacist    2 Market
            $table->string("notes")->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
