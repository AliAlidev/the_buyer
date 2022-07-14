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
            $table->string('code')->nullable(true);
            $table->string('name')->nullable(false);
            $table->string('dose')->nullable(true);
            $table->string('tab_count')->nullable(true);
            $table->string('med_shape')->nullable(true);
            $table->string('med_comp')->nullable(true);
            $table->text('effict_matterials')->nullable(true);
            $table->string('treatement_group')->nullable(true);
            $table->text('treatements')->nullable(true);
            $table->text('special_alarms')->nullable(true);
            $table->text('interference')->nullable(true);
            $table->text('side_effects')->nullable(true);
            $table->string('has_parts')->nullable(true);
            $table->float('num_of_parts')->default(0);
            $table->text('description')->nullable(true);
            $table->integer('merchant_type')->default(0);
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
