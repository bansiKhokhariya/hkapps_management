<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdsMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads_master', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tel_id');
            $table->string('cid');
            $table->string('ads_master');
            $table->integer('company_master_id')->unsigned()->nullable();
            $table->foreign('company_master_id')->references('id')->on('company_master');
            $table->softDeletes();
            $table->timestamps();
        });
//        Schema::table('ads_master', function ($table) {
//            $table->foreign('company_master_id')->references('id')->on('company_master');
//        });
    }

     /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ads_master');
    }
}
