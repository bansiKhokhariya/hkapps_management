<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdsProVersionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads_pro_version', function (Blueprint $table) {
            $table->increments('id');
            $table->string('adsProVersion');
            $table->integer('company_master_id')->unsigned()->nullable();
            $table->foreign('company_master_id')->references('id')->on('company_master');
            $table->timestamps();
        });
//        Schema::table('ads_pro_version', function ($table) {
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
        Schema::dropIfExists('ads_pro_version');
    }
}
