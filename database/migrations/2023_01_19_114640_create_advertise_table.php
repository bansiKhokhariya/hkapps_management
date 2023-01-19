<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertiseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('advertise', function (Blueprint $table) {
            $table->increments('id');
            $table->string('app_name');
            $table->string('app_packageName');
            $table->string('app_logo');
            $table->string('app_banner');
            $table->text('app_shortDecription');
            $table->string('app_buttonName');
            $table->string('app_rating');
            $table->string('app_download');
            $table->string('app_AdFormat');
            $table->integer('company_master_id')->unsigned()->nullable();
//            $table->foreign('company_master_id')->references('id')->on('hk_apps_management.company_master')->onDelete('cascade')->onUpdate('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('advertise', function ($table) {
            $table->foreign('company_master_id')->references('id')->on('hk_apps_management.company_master');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advertise');
    }
}
