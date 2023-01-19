<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdPlacementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql4')->create('ad_placement', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('allApps_id')->unsigned()->nullable();
            $table->integer('platform_id')->unsigned()->nullable();
            $table->string('ad_loadAdIdsType')->nullable();
            $table->text('ad_AppID')->nullable();
            $table->text('ad_Banner')->nullable();
            $table->text('ad_Interstitial')->nullable();
            $table->text('ad_Native')->nullable();
            $table->text('ad_NativeBanner')->nullable();
            $table->text('ad_RewardedVideo')->nullable();
            $table->text('ad_RewardedInterstitial')->nullable();
            $table->text('ad_AppOpen')->nullable();
//            $table->foreign('allApps_id')->references('id')->on('all_apps')->onDelete('cascade')->onUpdate('cascade');
//            $table->foreign('platform_id')->references('id')->on('platform')->onDelete('cascade')->onUpdate('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('ad_placement', function($table)
        {
            $table->foreign('allApps_id')->references('id')->on('all_apps');
            $table->foreign('platform_id')->references('id')->on('platform');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ad_placement');
    }
}
