<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql4')->create('app_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('app_packageName')->unsigned()->nullable();
            $table->text('description')->nullable();
            $table->text('descriptionHTML')->nullable();
            $table->text('summary')->nullable();
            $table->string('installs')->nullable();
            $table->integer('minInstalls')->nullable();
            $table->integer('realInstalls')->nullable();
            $table->integer('score')->nullable();
            $table->integer('ratings')->nullable();
            $table->integer('reviews')->nullable();
            $table->string('histogram')->nullable();
            $table->integer('price')->nullable();
            $table->string('free')->nullable();
            $table->string('currency')->nullable();
            $table->string('sale')->nullable();
            $table->string('saleTime')->nullable();
            $table->string('originalPrice')->nullable();
            $table->string('saleText')->nullable();
            $table->string('offersIAP')->nullable();
            $table->string('inAppProductPrice')->nullable();
            $table->string('developer')->nullable();
            $table->string('developerId')->nullable();
            $table->string('developerEmail')->nullable();
            $table->string('developerWebsite')->nullable();
            $table->string('developerAddress')->nullable();
            $table->string('genre')->nullable();
            $table->string('genreId')->nullable();
            $table->string('headerImage')->nullable();
            $table->string('screenshots')->nullable();
            $table->string('video')->nullable();
            $table->string('videoImage')->nullable();
            $table->string('contentRating')->nullable();
            $table->string('contentRatingDescription')->nullable();
            $table->string('adSupported')->nullable();
            $table->string('containsAds')->nullable();
            $table->string('released')->nullable();
            $table->string('updated')->nullable();
            $table->string('version')->nullable();
            $table->string('recentChanges')->nullable();
            $table->string('recentChangesHTML')->nullable();
            $table->string('comments')->nullable();
            $table->string('url')->nullable();
            $table->string('status')->nullable();
//            $table->foreign('app_packageName')->references('id')->on('all_apps')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::table('app_details', function($table)
        {
            $table->foreign('app_packageName')->references('id')->on('all_apps');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_details');
    }
}
