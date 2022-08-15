<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('data',function(Blueprint $table){
            $table->renameColumn('med_shape','shape_id')->change();
            $table->renameColumn('med_comp','comp_id')->change();
            $table->integer('minimum_amount')->default(0);
            $table->integer('maximum_amount')->default(0);
            $table->dropColumn('effict_matterials');
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
