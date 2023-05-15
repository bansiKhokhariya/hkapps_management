<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleAdmanagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_admanager', function (Blueprint $table) {
            $table->integer('id');
            $table->string('name')->nullable();
            $table->string('jsonFilePath')->nullable();
            $table->string('currentNetworkCode')->nullable();
            $table->string('advertise_id')->nullable();
            $table->string('order_id')->nullable();
            $table->string('trafficker_id')->nullable();
            $table->string('web_property_code')->nullable();
            $table->string('placementId')->nullable();
            $table->string('lineItemId')->nullable();
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
        Schema::dropIfExists('google_admanager');
    }
}
