<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpyAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql4')->create('spy_apps', function (Blueprint $table) {
            $table->id();
            $table->string('packageName');
            $table->string('url');
            $table->string('locale');
            $table->string('country');
            $table->string('name');
            $table->string('description');
            $table->string('developerName');
            $table->string('icon');
            $table->string('screenshots');
            $table->string('score');
            $table->string('priceText');
            $table->string('installsText');
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
        Schema::dropIfExists('spy_apps');
    }
}
