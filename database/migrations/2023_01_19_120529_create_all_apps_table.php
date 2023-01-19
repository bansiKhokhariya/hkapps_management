<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql4')->create('all_apps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('app_logo')->nullable();
            $table->string('app_name')->nullable();
            $table->string('app_packageName')->nullable();
            $table->text('app_apikey')->nullable();
            $table->text('app_note')->nullable();
            $table->integer('app_updateAppDialogStatus')->default(0);
            $table->string('app_versionCode')->nullable();
            $table->integer('app_redirectOtherAppStatus')->default(0);
            $table->string('app_newPackageName')->nullable();
            $table->string('app_privacyPolicyLink')->nullable();
            $table->string('app_accountLink')->nullable();
            $table->text('app_extra')->nullable();
            $table->integer('app_adShowStatus')->default(1);
            $table->integer('app_AppOpenAdStatus')->default(1);
            $table->string('app_howShowAd')->nullable();
            $table->string('app_adPlatformSequence')->nullable();
            $table->string('app_alternateAdShow')->nullable();
            $table->integer('app_testAdStatus')->default(0);
            $table->integer('app_mainClickCntSwAd')->nullable();
            $table->integer('app_innerClickCntSwAd')->nullable();
            $table->text('app_parameter')->nullable();
            $table->string('status')->nullable();
            $table->integer('company_master_id')->unsigned()->nullable();
//            $table->foreign('company_master_id')->references('id')->on('hk_apps_management.company_master')->onDelete('cascade')->onUpdate('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('all_apps', function ($table) {
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
        Schema::dropIfExists('all_apps');
    }
}
