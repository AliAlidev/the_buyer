<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTreatementGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treatement_group', function (Blueprint $table) {
            $table->id();
            $table->integer('tg_id');
            $table->string('ar_name')->nullable();
            $table->string('en_name')->nullable();
            $table->string('merchant_type');
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
        Schema::dropIfExists('treatement_group');
    }
}
