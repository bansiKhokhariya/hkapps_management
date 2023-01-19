<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlatformTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql4')->create('platform', function (Blueprint $table) {
            $table->increments('id');
            $table->string('logo');
            $table->string('platform_name');
            $table->string('ad_format');
            $table->string('status')->default(0);
            $table->integer('company_master_id')->unsigned()->nullable();
//            $table->foreign('company_master_id')->references('id')->on('hk_apps_management.company_master')->onDelete('cascade')->onUpdate('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('platform', function ($table) {
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
        Schema::dropIfExists('platform');
    }
}
