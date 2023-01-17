<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartyMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('party_master', function (Blueprint $table) {
            $table->increments('id');
            $table->string('party');
            $table->string('company_name');
            $table->integer('company_master_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('party_master', function ($table) {
            $table->foreign('company_master_id')->references('id')->on('company_master');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('party_master');
    }
}
