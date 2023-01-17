<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApikeyListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apikey_list', function (Blueprint $table) {
            $table->id();
            $table->text('apikey_text')->nullable();
            $table->string('apikey_packageName')->nullable();
            $table->integer('apikey_appID')->nullable();
            $table->integer('apikey_request')->default(1);
            $table->integer('company_master_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('apikey_list', function ($table) {
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
        Schema::dropIfExists('apikey_list');
    }
}
